<?php

namespace Jidaikobo\Kontiki\Controllers\FileControllerTraits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait JavaScriptTrait
{
    /**
     * Serve the requested JavaScript file.
     *
     * @return Response
     */
    public function serveJs(Request $request, Response $response): Response
    {
        $content = $this->view->fetch(
            'js/fileManager.js.php',
            [
                'uploading' => __('uploading'),
                'couldnt_upload' => __('couldnt_upload', "Could not upload"),
                'get_file_list' => __('get_file_list'),
                'confirm_delete_message' => __('confirm_delete_message', 'The item will be permanently deleted. Are you sure you want to delete this item?'),
                'couldnt_find_file' => __('couldnt_find_file'),
                'couldnt_get_file_list' => __('couldnt_get_file_list'),
                'copied' => __('copied'),
                'copy_failed' => __('copy_failed'),
                'close' => __('close'),
                'edit' => __('edit'),
                'couldnt_delete_file' => __('couldnt_delete_file'),
                'insert_success' => __('insert_success'),
            ]
        );
        $response->getBody()->write($content);

        return $response
            ->withoutHeader('Pragma')
            ->withHeader('Cache-Control', 'public, max-age=3600')
            ->withHeader('Content-Type', 'application/javascript; charset=utf-8')
            ->withStatus(200);
    }

    /**
     * Serve the requested JavaScript file.
     *
     * @return Response
     */
    public function serveInstanceJs(Request $request, Response $response): Response
    {
        $content = $this->view->fetch(
            'js/fileManagerInstance.js.php',
            [
                'basepath' => env('BASEPATH', '')
            ]
        );
        $response->getBody()->write($content);
        return $response
            ->withoutHeader('Pragma')
            ->withHeader('Cache-Control', 'public, max-age=3600')
            ->withHeader('Content-Type', 'application/javascript; charset=utf-8')
            ->withStatus(200);
    }
}
