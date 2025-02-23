<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

trait IndexExpiredTrait
{
    public function indexExpired(Request $request, Response $response): Response
    {
        return $this->index($request, $response, 'expired');
    }
}
