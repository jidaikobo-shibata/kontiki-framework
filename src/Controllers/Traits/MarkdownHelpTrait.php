<?php

namespace Jidaikobo\Kontiki\Controllers\Traits;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

trait MarkdownHelpTrait
{
    public function showMarkdownHelp(Request $request, Response $response): Response
    {
        $helpText = __DIR__ . '/../../locale/' . env('LANG') . '/file/markdown-help.php';
        $content = file_get_contents($helpText);

        return $this->renderResponse(
            $response,
            __("markdown_help", 'Markdown Help'),
            $content,
            'markdown/help.php'
        );
    }
}
