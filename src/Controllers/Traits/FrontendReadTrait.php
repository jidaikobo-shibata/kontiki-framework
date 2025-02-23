<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

trait FrontendReadTrait
{
    public function frontendReadBySlug(Request $request, Response $response, array $args): Response
    {
        $slug = $args['slug'];
        $data = [
            'body' => $this->model->getByFieldWithCondtioned('slug', $slug, 'published')
        ];
        return $this->jsonResponse($response, $data);
    }
}
