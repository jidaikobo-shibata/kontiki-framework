<?php

namespace jidaikobo\kontiki\Utils;

class MessageUtils
{
    public static function generateErrorMessages(array $errors, $model): string
    {
        // フィールド定義を取得
        $fieldDefinitions = $model->getFieldDefinitions();

        $html = '<div class="errormessages">';
        $html .= '<ul class="alert alert-danger p-3 ps-5 pt-0 mt-3 mb-3 fs-6">';

        foreach ($errors as $field => $messages) {
            // フォーム全体のエラー
            if ($field === 0) {
                $html .= '<li class="pt-3">' . Lang::get('found_the_problem', 'Found the problem');
                $html .= '<ul class="ps-3">';
                foreach ($messages as $message) {
                    $html .= sprintf(
                        '<li class="pt-2">%s</li>',
                        htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
                    );
                }
                $html .= '</ul></li>';
                continue;
            }

            // フィールド固有のエラー
            $id = FormUtils::nameToId($field);

            $html .= sprintf(
                '<li id="errormessage_%s" class="pt-3">',
                htmlspecialchars($id, ENT_QUOTES, 'UTF-8')
            );

            // モデルからラベルを取得
            $labelText = $fieldDefinitions[$field]['label'] ?? ucfirst($field);

            $html .= sprintf(
                '<a href="#%s" class="alert-link">%s</a>',
                htmlspecialchars($id, ENT_QUOTES, 'UTF-8'),
                sprintf(
                    Lang::get('error_at_label', 'Error at %s'),
                    htmlspecialchars($labelText, ENT_QUOTES, 'UTF-8')
                )
            );

            $html .= '<ul class="ps-3">';
            foreach ($messages as $message) {
                $html .= sprintf(
                    '<li class="pt-2">%s%s</li>',
                    $labelText,
                    htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
                );
            }
            $html .= '</ul></li>';
        }

        $html .= '</ul></div>';

        return $html;
    }

    /**
     * Generate HTML for success messages from an associative array.
     *
     * @param array $successMessages Associative array where the key is the form element's name
     *                               and the value is a success message.
     * @return string HTML string of the success messages.
     */
    public static function generateSuccessMessages(array $successMessages): string
    {
        $html = '<div class="successmessages">';
        foreach ($successMessages as $field => $message) {
            $id = FormUtils::nameToId($field);
            $html .= sprintf(
                '<div id="success_%s" class="alert alert-success">%s</div>',
                htmlspecialchars($id, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
            );
        }
        $html .= '</div>';

        return $html;
    }
}
