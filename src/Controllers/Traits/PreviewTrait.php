<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

trait PreviewTrait
{
    public function handlePreviewById(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $id = $args['id'];
        $data = $this->model->getById($id);
        return static::renderPreview($response, $data);
    }

    public function handlePreview(Request $request, Response $response): Response
    {
        $data = $this->model->getDataForForm('preview', $this->flashManager);
        return static::renderPreview($response, $data);
    }

    protected function setPreviewPath(): void
    {
        $projectPath = env('PROJECT_PATH', '');
        $dir = file_exists($projectPath . '/app/views/' . $this->adminDirName) ? 'app' : 'src' ;
        $previewPath = $projectPath . '/' . $dir . '/views/' . $this->adminDirName;
        $this->previewRenderer = new PhpRenderer($previewPath);
    }

    protected function renderPreview(Response $response, array $data): Response
    {
        if (!isset($data['title']) || !isset($data['content'])) {
            return $this->renderResponse(
                $response,
                __('cannot_preview_title'),
                __('cannot_preview_desc'),
                'preview/content.php'
            );
        } else {
            static::setPreviewPath();
            return $this->previewRenderer->render(
                $response,
                'preview.php',
                [
                    'title' => $data['title'],
                    'content' => $data['content'],
                ]
            );
        }
    }
}
