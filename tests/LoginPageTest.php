<?php

namespace Jidaikobo\Kontiki;

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

class LoginPageTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        $this->app = Bootstrap::init('development');
    }

    public function testLoginPageReturns200()
    {
        // not logged in
        $_SESSION = [];

        // GET /login
        $uri = $this->app->getBasePath() . '/login';

        $request = (new ServerRequestFactory())->createServerRequest('GET', $uri)
            ->withUri(new \Slim\Psr7\Uri('', '', 80, $uri));

        $response = $this->app->handle($request);

        // check status code 200
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testFileManagerJsReturns200WhenLoggedIn()
    {
        $_SESSION = [
            'jidaikobo\kontiki\auth' => [
                'user' => [
                    'id' => 1,
                    'username' => 'admin',
                    'role' => 'admin',
                ]
            ]
        ];

        $uri = $this->app->getBasePath() . '/fileManager.js';

        $request = (new ServerRequestFactory())->createServerRequest('GET', $uri)
            ->withUri(new \Slim\Psr7\Uri('', '', 80, $uri));

        $response = $this->app->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testFileManagerJsReturns404WhenNotLoggedIn()
    {
        $_SESSION = [];

        $uri = $this->app->getBasePath() . '/fileManager.js';

        $request = (new ServerRequestFactory())->createServerRequest('GET', $uri)
            ->withUri(new \Slim\Psr7\Uri('', '', 80, $uri));

        $response = $this->app->handle($request);

        $this->assertEquals(404, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        restore_error_handler();
        restore_exception_handler();
    }
}
