<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait IndexNormalTrait
{
    public function normalIndex(Request $request, Response $response): Response
    {
        $this->context = 'normal';
        return static::index($request, $response);
    }
}
