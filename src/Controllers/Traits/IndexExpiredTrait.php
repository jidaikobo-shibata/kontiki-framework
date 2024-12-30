<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait IndexExpiredTrait
{
    public function expiredIndex(Request $request, Response $response): Response
    {
        $this->context = 'expired';
        self::isUsesTrashRestoreTrait();
        return static::index($request, $response);
    }
}
