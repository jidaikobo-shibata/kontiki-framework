<?php

namespace jidaikobo\kontiki\Utils;
namespace jidaikobo\kontiki\Models\ModelInterface;

class TableHandler
{
    public function addErrors(string $content, array $errors, ModelInterface $model): string
    {
        return MessageUtils::errorHtml($errors, $model) . $content;
    }

    public function addSuccessMessages(string $content, array $successMessages): string
    {
        return MessageUtils::alertHtml($successMessages) . $content;
    }
}
