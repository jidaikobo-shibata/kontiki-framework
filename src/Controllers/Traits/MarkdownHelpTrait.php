<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait MarkdownHelpTrait
{
    public function showMarkdownHelp(Request $request, Response $response): Response
    {
        $helpText = __DIR__ . '/../../locale/' . env('APPLANG') . '/file/markdown-help.php';
        $content = file_get_contents($helpText);

        return $this->renderResponse(
            $response,
            __("markdown_help", 'Markdown Help'),
            $content,
            'markdown/help.php'
        );
    }
}
