<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait IndexPublishedTrait
{
    public function publishedIndex(Request $request, Response $response): Response
    {
        $this->context = 'published';
        return static::index($request, $response);
    }
}
