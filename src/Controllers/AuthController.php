<?php

namespace Jidaikobo\Kontiki\Controllers;

use Jidaikobo\Kontiki\Core\Auth;
use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Models\UserModel;
use Jidaikobo\Kontiki\Services\FormService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\App;

class AuthController extends BaseController
{
    protected UserModel $model;

    protected function setModel(): void
    {
        $this->model = new UserModel();
    }

    public static function registerRoutes(App $app, string $basePath = ''): void
    {
        $app->get('/login', AuthController::class . ':showLoginForm')->setName('login');
        $app->post('/login', AuthController::class . ':processLogin');
        $app->get('/logout', AuthController::class . ':logout');
    }

    /**
     * Display the login form.
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function showLoginForm(Request $request, Response $response): Response
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        if ($this->isIpBlocked($ip)) {
            http_response_code(403);
            die("Access denied due to too many failed login attempts.");
        }

        $data = $this->flashManager->getData('data', ['username' => '']);

        $formService = new FormService($this->view, $this->model);

        $content = $this->view->fetch('auth/login.php', $data);
        $content = $formService->addMessages(
            $content,
            $this->flashManager->getData('errors', [])
        );

        return $this->renderResponse(
            $response,
            __('login', 'Login'),
            $content,
            'layout-simple.php'
        );
    }

    /**
     * Handle the login form submission.
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function processLogin(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

        // Validate Login
        if (Auth::getInstance()->login($username, $password)) {
            $this->resetRateLimit($ip);
            $this->cleanOldRateLimitData();
            return $this->redirectResponse($request, $response, 'dashboard');
        }

        // Login Failed
        $this->recordFailedLogin($ip);
        $this->flashManager->addErrors([
            ['messages' => [__('wrong_username_or_password', 'Incorrect username or password')]],
        ]);
        $this->flashManager->setData('data', ['username' => $username]); // not keep password
        return $this->redirectResponse($request, $response, 'login');
    }

    public function logout(Request $request, Response $response): Response
    {
        Auth::getInstance()->logout();
        return $this->redirectResponse($request, $response, 'login');
    }

    private function recordFailedLogin(string $ip): void
    {
        $db = Database::getInstance()->getConnection();

        $now = time();

        // Get existing rate limit data
        $record = $db->table('rate_limit')
            ->where('ip_address', $ip)
            ->first();

        if ($record) {
            // If existing data exists, update the number of failures
            $db->table('rate_limit')
                ->where('ip_address', $ip)
                ->update([
                    'attempt_count' => $record->attempt_count + 1,
                    'last_attempt' => $now,
                ]);
        } else {
            // Insert as new data
            $db->table('rate_limit')->insert([
                'ip_address' => $ip,
                'attempt_count' => 1,
                'first_attempt' => $now,
                'last_attempt' => $now,
            ]);
        }
    }

    function isIpBlocked(string $ip): bool
    {
        $db = Database::getInstance()->getConnection();
        $record = $this->getRateLimitRecord($db, $ip);

        if (!$record) {
            return false; // No record means no limit
        }

        if ($this->isCurrentlyBlocked($record)) {
            return true;
        }

        if ($this->shouldBlockIp($record)) {
            $this->blockIp($db, $ip);
            return true;
        }

        return false;
    }

    /**
     * Get the rate limit record for the given IP.
     */
    private function getRateLimitRecord($db, string $ip)
    {
        return $db->table('rate_limit')
            ->where('ip_address', $ip)
            ->first();
    }

    /**
     * Check if the IP is currently blocked.
     */
    private function isCurrentlyBlocked($record): bool
    {
        return !is_null($record->blocked_until) && $record->blocked_until > time();
    }

    /**
     * Determine if the IP should be blocked based on rate limit conditions.
     */
    private function shouldBlockIp($record): bool
    {
        $limitDuration = 180; // 3 minutes
        $maxAttempts = 5;     // max 5 attempts

        return $record->attempt_count >= $maxAttempts &&
               (time() - $record->first_attempt) <= $limitDuration;
    }

    /**
     * Block the IP by updating the database.
     */
    private function blockIp($db, string $ip): void
    {
        $blockDuration = 900; // block for 15 minutes
        $db->table('rate_limit')
            ->where('ip_address', $ip)
            ->update(['blocked_until' => time() + $blockDuration]);
    }

    private function resetRateLimit(string $ip): void
    {
        $db = Database::getInstance()->getConnection();

        $db->table('rate_limit')
            ->where('ip_address', $ip)
            ->delete();
    }

    private function cleanOldRateLimitData(): void
    {
        $db = Database::getInstance()->getConnection();

        $threshold = time() - (7 * 24 * 60 * 60); // 7 days

        $db->table('rate_limit')
            ->where('last_attempt', '<', $threshold)
            ->delete();
    }

}
