<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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
