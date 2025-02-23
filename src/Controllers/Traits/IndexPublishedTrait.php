<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

trait IndexPublishedTrait
{
    public function indexPublished(Request $request, Response $response): Response
    {
        return $this->index($request, $response, 'published');
    }
}
