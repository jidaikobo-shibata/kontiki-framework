<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait FrontendIndexTrait
{
    public function frontendIndex(Request $request, Response $response): Response
    {
        $this->context = 'normal';
        $baseurl = env('BASEURL') . '/' . $this->postType;
        $data = $this->getIndexData($request->getQueryParams());
        $data = [
            'body' => $data,
            'pagination' => $this->pagination->render($baseurl),
        ];
        return $this->jsonResponse($response, $data);
    }
}
