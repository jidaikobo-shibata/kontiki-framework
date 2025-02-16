## How to add Route and Condition

### Controller Sample

see also BaseController::registerRoutes()

```Controller
    public static function registerRoutes(App $app, string $basePath = ''): void
    {
        parent::registerRoutes($app, $basePath);

        $controllerClass = static::class;

        $app->group(
            '/admin/' . $basePath,
            function (RouteCollectorProxy $group) use ($controllerClass, $basePath) {
                $group->get('/index/draft', [$controllerClass, 'draftIndex'])->setName("{$basePath}_index_draft");
            }
        )->add(AuthMiddleware::class);
    }

    public function draftIndex(Request $request, Response $response): Response
    {
        // see also PostModel::getAdditionalConditions()
        $this->context = 'draft';
        return static::index($request, $response);
    }
```

### Model Sample

see also PostModel::getAdditionalConditions()

```Model
    Private Function Applydraftconditions(Builder $Query): Builder
    {
        Return $Query->Where('Status', '=', 'Draft');
    }

    Private Function Applynotdraftconditions(Builder $Query): Builder
    {
        Return $Query->Where('Status', '=', 'Published');
    }
}
```

## Add Controllers' Original Routes

direct add to Routes.php.
///
        $app->get('/info', [Controller\TestController::class, 'test'])
            ->setName('test')
            ->add($container->get(AuthMiddleware::class));
///