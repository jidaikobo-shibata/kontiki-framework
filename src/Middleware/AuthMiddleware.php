<?php

namespace Jidaikobo\Kontiki\Middleware;

use Jidaikobo\Kontiki\Core\Auth;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\PhpRenderer;

class AuthMiddleware implements MiddlewareInterface
{
    private PhpRenderer $view;
    private Auth $auth;
    private array $excludedRoutes = [
        '/favicon.ico',
        '/login',
        '/logout',
    ];

    public function __construct(
        PhpRenderer $view,
        Auth $auth
    ) {
        $this->auth = $auth;
        $this->view = $view;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $path = '/' . basename($request->getUri()->getPath());

        // for guest routes
        if (in_array($path, $this->excludedRoutes, true)) {
            return $handler->handle($request);
        }

        // for login users
        if (!$this->auth->isLoggedIn()) {
            $response = new \Slim\Psr7\Response();
            $content = $this->view->fetch('error/404.php');
            return $this->view->render(
                $response->withHeader('Content-Type', 'text/html')->withStatus(404),
                'layout-error.php',
                [
                    'pageTitle' => __('404_text'),
                    'content' => $content
                ]
            );
        }

        return $handler->handle($request);
    }
}
