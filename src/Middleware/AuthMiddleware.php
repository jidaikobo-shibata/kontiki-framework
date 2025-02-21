<?php

namespace Jidaikobo\Kontiki\Middleware;

use Jidaikobo\Kontiki\Services\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\PhpRenderer;

class AuthMiddleware implements MiddlewareInterface
{
    private AuthService $authService;
    private PhpRenderer $view;

    public function __construct(AuthService $authService, PhpRenderer $view)
    {
        $this->authService = $authService;
        $this->view = $view;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        if (!$this->authService->isLoggedIn()) {
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
