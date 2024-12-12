<?php

namespace jidaikobo\kontiki\Controllers;

use Slim\Views\PhpRenderer;
use jidaikobo\kontiki\Services\SidebarService;

abstract class BaseController
{
    protected PhpRenderer $view;

    public function __construct(PhpRenderer $view, SidebarService $sidebarService)
    {
        $this->view = $view;
        $this->view->setAttributes(['sidebarItems' => $sidebarService->getLinks()]);
    }
}
