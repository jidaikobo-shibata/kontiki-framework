<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait IndexReservedTrait
{
    public function reservedIndex(Request $request, Response $response): Response
    {
        $this->context = 'reserved';
        self::isUsesTrashRestoreTrait();
        return static::index($request, $response);
    }
}
