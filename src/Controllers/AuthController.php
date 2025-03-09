<?php

namespace Jidaikobo\Kontiki\Controllers;

use Jidaikobo\Kontiki\Core\Auth;
use Jidaikobo\Kontiki\Models\UserModel;
use Jidaikobo\Kontiki\Services\FormService;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

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
        if (Auth::getInstance()->login($username, $password)) {
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
        Auth::getInstance()->logout();
        return $this->redirectResponse($request, $response, 'login');
    }
}
