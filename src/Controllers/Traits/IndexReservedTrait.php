<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait IndexReservedTrait
{
    public function indexReserved(Request $request, Response $response): Response
    {
        return $this->index($request, $response, 'reserved');
    }
}
