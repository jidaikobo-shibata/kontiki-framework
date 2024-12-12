<?php

namespace jidaikobo\kontiki\Controllers;

use jidaikobo\kontiki\Utils\Lang;
use jidaikobo\kontiki\Services\SidebarService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollector;
use Slim\Routing\RouteParser;
use Slim\Views\PhpRenderer;

class DashboardController
{
    private PhpRenderer $view;
    private RouteParser $routeParser;
    private RouteCollector $routeCollector;

    public function __construct(PhpRenderer $view, SidebarService $sidebarService)
    {
        $this->view = $view;
        $this->view->setAttributes(['sidebarItems' => $sidebarService->getLinks()]);
    }

    public function dashboard(Request $request, Response $response): Response
    {
        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => Lang::get('management_portal', 'Management Portal'),
                'content' => '',
            ]
        );
        ;
    }
}
