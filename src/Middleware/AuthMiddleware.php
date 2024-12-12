<?php

namespace jidaikobo\kontiki\Middleware;

use jidaikobo\kontiki\Utils\Lang;
use Aura\Session\Session;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\PhpRenderer;

class AuthMiddleware implements MiddlewareInterface
{
    private Session $session;
    private PhpRenderer $view;

    public function __construct(Session $session, PhpRenderer $view)
    {
        $this->session = $session;
        $this->view = $view;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        // セッションからユーザー情報を取得
        $segment = $this->session->getSegment('jidaikobo\kontiki\Session');
        $user = $segment->get('user');

        // 未ログインの場合は 404 ページを表示（詳細なエラーは返さない）
        if (!$user) {
            $response = new \Slim\Psr7\Response();
            $content = $this->view->fetch('error/404.php');
            return $this->view->render(
                $response->withHeader('Content-Type', 'text/html')->withStatus(404),
                'layout-error.php',
                [
                    'pageTitle' => Lang::get('404_text'),
                    'content' => $content
                ]
            );
            ;
        }

        // ログイン中の場合は次のミドルウェアまたはコントローラーに進む
        return $handler->handle($request);
    }
}
