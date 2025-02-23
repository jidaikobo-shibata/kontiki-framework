<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

trait IndexReservedTrait
{
    public function indexReserved(Request $request, Response $response): Response
    {
        return $this->index($request, $response, 'reserved');
    }
}
