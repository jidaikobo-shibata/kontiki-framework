<?php

namespace Jidaikobo\Kontiki\Controllers;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Models\UserModel;
use Jidaikobo\Kontiki\Services\FormService;
use Jidaikobo\Kontiki\Services\AuthService;
use Jidaikobo\Kontiki\Utils\FormHandler;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\PhpRenderer;
use Valitron\Validator;

class AuthController extends BaseController
{
    private AuthService $authService;
    protected UserModel $model;

    public function __construct(
        PhpRenderer $view,
        Session $session,
        AuthService $authService
    ) {
        parent::__construct($view, $session);
        $this->authService = $authService;
    }

    protected function setModel(): void
    {
        $db = Database::getInstance()->getConnection();
        $this->model = new UserModel($db);
    }

    public static function registerRoutes(App $app, string $basePath = ''): void
    {
        $app->get('/login',  AuthController::class . ':showLoginForm')->setName('login');
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

        // Validate Login
        if ($this->authService->login($username, $password)) {
            return $this->redirectResponse($request, $response, 'dashboard');
        }

        // Login Failed
        $this->flashManager->addErrors([
            ['messages' => [__('wrong_username_or_password', 'Incorrect username or password')]],
        ]);
        $this->flashManager->setData('data', ['username' => $username]); // not keep password
        return $this->redirectResponse($request, $response, 'login');
    }

    public function logout(Request $request, Response $response): Response
    {
        $this->authService->logout();
        return $this->redirectResponse($request, $response, 'login');
    }
}
