<?php

namespace jidaikobo\kontiki\Controllers;

use Aura\Session\Session;
use jidaikobo\kontiki\Middleware\AuthMiddleware;
use jidaikobo\kontiki\Models\ModelInterface;
use jidaikobo\kontiki\Services\SidebarService;
use jidaikobo\kontiki\Services\FormService;
use jidaikobo\kontiki\Utils\Env;
use jidaikobo\kontiki\Utils\CsrfManager;
use jidaikobo\kontiki\Utils\FlashManager;
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
    protected FormService $formService;
    protected string $table;
    protected CsrfManager $csrfManager;
    protected FlashManager $flashManager;

    /**
     * Constructor
     *
     * Initializes the BaseController with its dependencies.
     *
     * @param PhpRenderer     $view           The view renderer.
     * @param SidebarService  $sidebarService The sidebar service.
     * @param Session         $session        The session manager.
     * @param ModelInterface  $model          The model instance.
     */
    public function __construct(
      PhpRenderer $view,
      SidebarService $sidebarService,
      Session $session,
      ModelInterface $model
    ) {
        $this->csrfManager = new CsrfManager($session);
        $this->flashManager = new FlashManager($session);
        $this->formService = new FormService($view, $model, $this->flashManager, $this->csrfManager);
        $this->model = $model;
        $this->table = $this->model->getTableName();
        $this->session = $session;
        $this->view = $view;
        $this->view->setAttributes(['sidebarItems' => $sidebarService->getLinks()]);
    }

    /**
     * Register routes for the controller.
     *
     * Defines the routing for this controller, based on traits.
     *
     * @param App    $app      The Slim application instance.
     * @param string $basePath The base path for the routes.
     *
     * @return void
     */
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
     * Resolve route class name from a trait name.
     *
     * Converts a trait name into the corresponding route class name.
     *
     * @param string $trait The fully qualified name of the trait.
     *
     * @return string The fully qualified name of the corresponding route class.
     */
    private static function resolveRouteClass(string $trait): string
    {
        $traitName = (new \ReflectionClass($trait))->getShortName();
        return "jidaikobo\\kontiki\\Controllers\\Routes\\" . str_replace('Trait', 'Routes', $traitName);
    }

    /**
     * Validate the CSRF token and handle errors if invalid.
     *
     * @param array    $data            The request data (e.g., POST body).
     * @param Request  $request         The current request instance.
     * @param Response $response        The current response instance.
     * @param string   $redirectTarget  The URL or route to redirect if validation fails.
     *
     * @return Response|null Returns a redirect response if invalid, or null if valid.
     */
    protected function validateCsrfToken(
        array $data,
        Request $request,
        Response $response,
        string $redirectTarget
    ): ?Response {
        if (empty($data['_csrf_value']) || !$this->csrfManager->isValid($data['_csrf_value'])) {
            $this->flashManager->addErrors([
                Lang::get("csrf_invalid", 'Invalid CSRF token.'),
            ]);
            return $this->redirectResponse($request, $response, $redirectTarget);
        }

        return null;
    }

    /**
     * Create a redirect response.
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $target      Route name or URL.
     * @param  array    $routeData   Route parameters (for named routes).
     * @param  int      $status      HTTP status code for the redirect (default: 302).
     *
     * @return Response
     */
    protected function redirectResponse(Request $request, Response $response, string $target, array $routeData = [], int $status = 302): Response
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
     * Render a response with the given content and template.
     *
     * @param Response $response       The Slim response object.
     * @param string   $pageTitle      The page title for the rendered view.
     * @param string   $content        The main content of the page.
     * @param string   $template       The template to use for rendering (default: 'layout.php').
     * @param array    $additionalData Additional data to pass to the view (default: []).
     *
     * @return Response The rendered response.
     */
    protected function renderResponse(
        Response $response,
        string $pageTitle,
        string $content,
        string $template = 'layout.php',
        array $additionalData = []
    ): Response {
        // Combine standard and additional data for the view
        $data = array_merge(
            [
                'pageTitle' => $pageTitle,
                'content' => $content,
            ],
            $additionalData
        );

        // Render the view and return the response
        return $this->view->render($response, $template, $data);
    }

    /**
     * Create a JSON response.
     *
     * @param Response $response The original response object.
     * @param array $data The data to be included in the JSON response.
     * @param int $status The HTTP status code.
     *
     * @return Response The modified response object with JSON content.
     */
    public static function jsonResponse(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
