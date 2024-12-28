<?php

namespace Jidaikobo\Kontiki\Utils;

use Jidaikobo\Kontiki\Models\ModelInterface;

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
