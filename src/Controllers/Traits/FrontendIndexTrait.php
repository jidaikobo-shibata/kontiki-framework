<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait FrontendIndexTrait
{
    public function frontendIndex(Request $request, Response $response): Response
    {
        $baseurl = env('BASEURL') . '/' . $this->adminDirName;
        $data = $this->model->getIndexData('published', $request->getQueryParams());
        $data = [
            'body' => $data,
            'pagination' => $this->model->getPagination()->render($baseurl),
        ];
        return $this->jsonResponse($response, $data);
    }
}
