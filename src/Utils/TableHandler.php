<?php

namespace jidaikobo\kontiki\Utils;

class TableHandler
{
    public function addErrors(string $content, array $errors): string
    {
        return MessageUtils::generateErrorMessages($errors) . $content;
    }

    public function addSuccessMessages(string $content, array $successMessages): string
    {
        return MessageUtils::generateSuccessMessages($successMessages) . $content;
    }
}
