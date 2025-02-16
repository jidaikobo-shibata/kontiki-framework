<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait IndexDraftTrait
{
    public function draftIndex(Request $request, Response $response): Response
    {
        // see also PostModel::getAdditionalConditions()
        $this->context = 'draft';
        return static::index($request, $response);
    }
}
