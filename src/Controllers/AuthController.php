<?php

namespace Jidaikobo\Kontiki\Controllers;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Models\UserModel;
use Jidaikobo\Kontiki\Services\FormService;
use Jidaikobo\Kontiki\Services\AuthService;
use Jidaikobo\Kontiki\Utils\CsrfManager;
use Jidaikobo\Kontiki\Utils\FlashManager;
use Jidaikobo\Kontiki\Utils\FormHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Views\PhpRenderer;
use Valitron\Validator;

class AuthController extends BaseController
{
    private AuthService $authService;

    public function __construct(
        PhpRenderer $view,
        Session $session,
        UserModel $userModel,
        AuthService $authService
    ) {
        parent::__construct($view, $session, $userModel);
        $this->authService = $authService;
    }

    public static function registerRoutes(App $app, string $basePath = ''): void
    {
        $app->get('/login',  [AuthController::class, 'showLoginForm'])->setName('login');
        $app->post('/login', [AuthController::class, 'processLogin']);
        $app->get('/logout', [AuthController::class, 'logout']);
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

        $content = $this->view->fetch('auth/login.php', $data);
        $content = $this->formService->addMessages(
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
