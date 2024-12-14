<?php

namespace jidaikobo\kontiki\Controllers;

use Aura\Session\Session;
use jidaikobo\kontiki\Models\User;
use jidaikobo\kontiki\Utils\FormHandler;
use jidaikobo\kontiki\Utils\Lang;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\PhpRenderer;
use Valitron\Validator;

class AuthController
{
    private Session $session;
    private PhpRenderer $view;
    private User $userModel;

    public function __construct(Session $session, PhpRenderer $view, User $userModel)
    {
        $this->session = $session;
        $this->view = $view;
        $this->userModel = $userModel;
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
        // セッションからエラーと入力値を取得
        $segment = $this->session->getSegment('jidaikobo\kontiki\auth');
        $error = $segment->get('error', []);
        $input = $segment->get(
            'input',
            [
            'username' => '',
            'password' => '',
            ]
        );

        // セッションから値を削除（1回限りの使用）
        $segment->clear();

        $data = [
            'input' => $input,
        ];

        // ログインフォームをレンダリング
        $content = $this->view->fetch('auth/login.php', $data);

        // エラーがある場合にDOMを加工
        if (!empty($error)) {
            $formHandler = new FormHandler($content);
            $formHandler->addErrors($error);
            $content = $formHandler->getHtml();
        }

        return $this->view->render(
            $response,
            'layout-simple.php',
            [
            'pageTitle' => Lang::get('login', 'Login'),
            'content' => $content
            ]
        );
        ;
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
        $data = $request->getParsedBody();
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        $routeContext = RouteContext::fromRequest($request);

        // バリデーションの実行
        $validationResult = $this->userModel->validate($data);
        if (!$validationResult['valid']) {
            $segment = $this->session->getSegment('jidaikobo\kontiki\auth');
            $segment->set('error', $validationResult['errors']);
            $segment->set(
                'input',
                [
                'username' => $username,
                'password' => '', // パスワードは再入力を促すため空にする
                ]
            );

            // ログインフォームにリダイレクト
            $routeContext = RouteContext::fromRequest($request);
            $loginUrl = $routeContext->getRouteParser()->urlFor('login');

            return $response
                ->withHeader('Location', $loginUrl)
                ->withStatus(302);
        }

        // ログイン検証
        $user = $this->userModel->getByUsername($username);
        $stored_password = $user['password'] ?? null ;

        if ($stored_password !== null && password_verify($password, $stored_password)) {
            // セッションにユーザー情報を保存
            $segment = $this->session->getSegment('jidaikobo\kontiki\auth');
            $segment->set('user', $user);

            $dashboardUrl = $routeContext->getRouteParser()->urlFor('dashboard');
            return $response
                ->withHeader('Location', $dashboardUrl)
                ->withStatus(302);
        }

        // ログイン失敗
        $segment = $this->session->getSegment('jidaikobo\kontiki\auth');
        $segment->set('error', [[Lang::get('wrong_username_or_password', 'Incorrect username or password')]]);
        $segment->set(
            'input',
            [
            'username' => $username,
            'password' => '', // パスワードは再入力を促すため空にする
            ]
        );

        $loginUrl = $routeContext->getRouteParser()->urlFor('login');
        return $response
            ->withHeader('Location', $loginUrl)
            ->withStatus(302);
    }

    public function logout(Request $request, Response $response): Response
    {
        $this->session->destroy();

        $routeContext = RouteContext::fromRequest($request);
        $loginUrl = $routeContext->getRouteParser()->urlFor('login');
        return $response
            ->withHeader('Location', $loginUrl)
            ->withStatus(302);
    }
}
