<?php

namespace jidaikobo\kontiki\Controllers;

use jidaikobo\kontiki\Services\SidebarService;
use Slim\Routing\RouteContext;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class BaseController
{
    protected PhpRenderer $view;

    public function __construct(PhpRenderer $view, SidebarService $sidebarService)
    {
        $this->view = $view;
        $this->view->setAttributes(['sidebarItems' => $sidebarService->getLinks()]);
    }

    /**
     * Create a redirect response.
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $target      Route name or URL.
     * @param  array    $routeData   Route parameters (for named routes).
     * @param  int      $status      HTTP status code for the redirect (default: 302).
     * @return Response
     */
    protected function redirect(Request $request, Response $response, string $target, array $routeData = [], int $status = 302): Response
    {
        // 名前付きルートの判定: プレースホルダーが含まれないURLと仮定
        if (strpos($target, '/') === 0 || filter_var($target, FILTER_VALIDATE_URL)) {
            // 任意URLへのリダイレクト
            $redirectUrl = $target;
        } else {
            // 名前付きルートのURLを生成
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $redirectUrl = $routeParser->urlFor($target, $routeData);
        }

        return $response
            ->withHeader('Location', $redirectUrl)
            ->withStatus($status);
    }
}
