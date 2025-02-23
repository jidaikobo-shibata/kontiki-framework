<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

trait IndexDraftTrait
{
    public function indexDraft(Request $request, Response $response): Response
    {
        return $this->index($request, $response, 'draft');
    }
}
