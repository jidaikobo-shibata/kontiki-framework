<?php

namespace Jidaikobo\Kontiki\Controllers;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Managers\CsrfManager;
use Jidaikobo\Kontiki\Managers\FlashManager;
use Jidaikobo\Kontiki\Middleware\AuthMiddleware;
use Jidaikobo\Kontiki\Services\GetRoutesService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Routing\RouteContext;
use Slim\Routing\RouteCollectorProxy;
use Slim\Views\PhpRenderer;

abstract class BaseController
{
    protected array $routes;
    protected string $adminDirName;

    protected CsrfManager $csrfManager;
    protected FlashManager $flashManager;
    protected FormService $formService;
    protected PhpRenderer $view;
    protected ?PhpRenderer $previewRenderer = null;

    /**
     * Constructor
     *
     * Initializes the BaseController with its dependencies.
     *
     * @param PhpRenderer       $view             The view renderer.
     * @param Session           $session          The session manager.
     * @param GetRoutesService  $GetRoutesService The sidebar service.
     */
    public function __construct(
        PhpRenderer $view,
        Session $session,
        ?GetRoutesService $getRoutesService = null
    ) {
        $this->view = $view;
        $this->csrfManager = new CsrfManager($session);
        $this->flashManager = new FlashManager($session);
        $this->setModel();
        $this->setRoutes($getRoutesService);
    }

    protected function setModel(): void
    {
    }

    protected function setRoutes($getRoutesService): void
    {
        if ($getRoutesService) {
            $this->view->setAttributes(['sidebarItems' => $getRoutesService->getSidebar()]);
            $this->routes = $getRoutesService->getLinks($this->adminDirName);
        }
    }

    public function getRoutes(): Array
    {
        return $this->routes;
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
        return "Jidaikobo\\Kontiki\\Controllers\\Routes\\" . str_replace('Trait', 'Routes', $traitName);
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
        ?array $data,
        Request $request,
        Response $response,
        string $redirectTarget
    ): ?Response {
        $data = $data ?? [];

        if (!$this->isCsrfTokenValid($data)) {
            $this->flashManager->addErrors([
                ['messages' => [__("csrf_invalid", 'Invalid CSRF token.')]],
            ]);
            return $this->redirectResponse($request, $response, $redirectTarget);
        }

        $this->csrfManager->regenerate();

        return null;
    }

    protected function validateCsrfForJson(?array $data, Response $response): ?Response
    {
        $data = $data ?? [];
        if (!$this->isCsrfTokenValid($data)) {

            $this->flashManager->addErrors([
                ['messages' => [__("csrf_invalid", 'Invalid CSRF token.')]],
            ]);
            return $this->jsonResponse($response, $data, 403);
        }

        $this->csrfManager->regenerate();

        return null;
    }

    private function isCsrfTokenValid(array $data): bool
    {
        return !empty($data['_csrf_value']) && $this->csrfManager->isValid($data['_csrf_value']);
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
            $redirectUrl = env('BASEPATH', '') . $target;
        } else {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $redirectUrl = $routeParser->urlFor($target, $routeData);
        }

        return $response
            ->withHeader('Location', $redirectUrl)
            ->withStatus($status);
    }

    /**
     * Render a response with the given content and template.
     *
     * @param Response $response       The Slim response object.
     * @param string   $pageTitle      The page title for the rendered view.
     * @param string   $content        The main content of the page.
     * @param string   $template       The template to use for rendering.
     * @param array    $additionalData Additional data to pass to the view.
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

        $cacheControl = 'no-store, no-cache, must-revalidate, max-age=0';
        $response = $response->withHeader('Cache-Control', $cacheControl)
                             ->withHeader('Pragma', 'no-cache')
                             ->withHeader('Expires', '0');

        // Output Buffering with Exception Handling
        ob_start();
        try {
            $response = $this->view->render($response, $template, $data);
            $output = ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean(); // Ensure buffer is cleared on error
            throw $e;
        }

        $response->getBody()->write($output);
        return $response;
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
    public static function jsonResponse(
        Response $response,
        array $data,
        int $status = 200
    ): Response {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
    }
}
