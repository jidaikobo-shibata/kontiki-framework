<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

trait IndexTaxonomyTrait
{
    public function indexTaxonomy(Request $request, Response $response): Response
    {
        return $this->index($request, $response, 'all');
    }
}
