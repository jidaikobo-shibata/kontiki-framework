<?php

namespace Jidaikobo\Kontiki\Handlers;

use Jidaikobo\Kontiki\Models\ModelInterface;
use Jidaikobo\Kontiki\Utils\MessageUtils;

class TableHandler
{
    public function addErrors(string $content, array $errors, ModelInterface $model): string
    {
        return MessageUtils::errorHtml($errors, $model) . $content;
    }

    public function addSuccessMessages(string $content, array $successMessages): string
    {
        $successMessage = join($successMessages);
        return MessageUtils::alertHtml($successMessage) . $content;
    }
}
