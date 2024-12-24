<?php

namespace jidaikobo\kontiki\Controllers;

use Aura\Session\Session;
use jidaikobo\kontiki\Database\DatabaseHandler;
use jidaikobo\kontiki\Middleware\AuthMiddleware;
use jidaikobo\kontiki\Models\ModelInterface;
use jidaikobo\kontiki\Services\SidebarService;
use jidaikobo\kontiki\Utils\Env;
use jidaikobo\kontiki\Utils\CsrfManager;
use jidaikobo\kontiki\Utils\FlashManager;
use jidaikobo\kontiki\Utils\FormHandler;
use jidaikobo\kontiki\Utils\FormRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteContext;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;

abstract class BaseController
{
    protected ModelInterface $model;
    protected PhpRenderer $view;
    protected Session $session;
    protected SidebarService $sidebarService;
    protected string $table;
    protected CsrfManager $csrfManager;
    protected FlashManager $flashManager;

    public function __construct(
      PhpRenderer $view,
      SidebarService $sidebarService,
      Session $session,
      ModelInterface $model
    ) {
        $this->csrfManager = new CsrfManager($session);
        $this->flashManager = new FlashManager($session);
        $this->model = $model;
        $this->table = $this->model->getTableName();
        $this->session = $session;
        $this->view = $view;
        $this->view->setAttributes(['sidebarItems' => $sidebarService->getLinks()]);
    }

    public static function registerRoutes(App $app, string $basePath = ''): void
    {
        $controllerClass = static::class;

        $app->group('/admin/' . $basePath, function (RouteCollectorProxy $group) use ($controllerClass, $basePath) {
            $traits = class_uses($controllerClass);

            foreach ($traits as $trait) {
                $routeClass = self::resolveRouteClass($trait);
                if (class_exists($routeClass) && method_exists($routeClass, 'register')) {
                    $routeClass::register($group, $basePath, $controllerClass);
                }
            }
        })->add(AuthMiddleware::class);
    }

    /**
     * トレイト名からルートクラス名を生成
     */
    private static function resolveRouteClass(string $trait): string
    {
        $traitName = (new \ReflectionClass($trait))->getShortName();
        return "jidaikobo\\kontiki\\Controllers\\Routes\\" . str_replace('Trait', 'Routes', $traitName);
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
        if (strpos($target, '/') === 0 || filter_var($target, FILTER_VALIDATE_URL)) {
            $redirectUrl = $target;
        } else {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $redirectUrl = $routeParser->urlFor($target, $routeData);
        }

        return $response
            ->withHeader('Location', Env::get('BASEPATH') . $redirectUrl)
            ->withStatus($status);
    }

    /**
     * Create a JSON response.
     *
     * @param Response $response The original response object.
     * @param array $data The data to be included in the JSON response.
     * @param int $status The HTTP status code.
     * @return Response The modified response object with JSON content.
     */
    public static function jsonResponse(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }

    protected function renderFormHtml(string $action, array $fields, string $description = '', string $buttonText = 'Submit'): string
    {
        $formRenderer = new FormRenderer($fields, $this->view);

        return $this->view->fetch(
            'forms/edit.php',
            [
                'actionAttribute' => Env::get('BASEPATH') . $action,
                'csrfToken' => $this->csrfManager->getToken(),
                'formHtml' => $formRenderer->render(),
                'description' => $description,
                'buttonText' => $buttonText,
            ]
        );
    }

    protected function renderForm(
        Response $response,
        string $action,
        string $title,
        array $fields,
        string $description = '',
        string $buttonText = 'Submit'
    ): Response {
        $content = $this->renderFormHtml($action, $fields, $description, $buttonText);

        $formHandler = new FormHandler($content, $this->model);
        $formHandler->addErrors($this->flashManager->getData('errors', []));
        $formHandler->addSuccessMessages($this->flashManager->getData('success', []));

        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => $title,
                'content' => $formHandler->getHtml(),
            ]
        );
    }
}
