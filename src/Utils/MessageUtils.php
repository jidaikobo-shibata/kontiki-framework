<?php

namespace jidaikobo\kontiki\Utils;

use jidaikobo\kontiki\Models\ModelInterface;
use jidaikobo\Log;

class MessageUtils
{
    public static function errorHtml(array $errors, ModelInterface $model): string
    {
        // フィールド定義を取得
        $fieldDefinitions = $model->getFieldDefinitions();

        $html = '<div class="errormessages">';
        $html .= '<ul class="alert alert-danger p-3 ps-5 pt-0 mt-3 mb-3 fs-6">';

        foreach ($errors as $field => $errorDetails) {
            $messages = $errorDetails['messages'] ?? [];
            $htmlName = $errorDetails['htmlName'] ?? $field;

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
            $id = FormUtils::nameToId($htmlName);

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
     * Generates a section for displaying status messages with appropriate styling.
     *
     * @param string $message The message to display within the section.
     * @param string $status The status of the message
     * @return string HTML output for the status section.
     */
    public static function alertHtml(string $message, string $status = "success"): string
    {
        // Define the CSS class based on the status
        $statusClass = '';
        switch ($status) {
            case 'success':
                $statusClass = 'alert alert-success';
                break;
            case 'info':
                $statusClass = 'alert alert-info';
                break;
            case 'warning':
                $statusClass = 'alert alert-warning';
                break;
            case 'danger':
                $statusClass = 'alert alert-danger';
                break;
            default:
                $statusClass = 'alert alert-secondary'; // Default class for undefined statuses
        }

        // Generate the HTML for the status section
        $html = '<section class="' . htmlspecialchars($statusClass) . '" role="status">';
        $html .= '<p class="mb-0">' . htmlspecialchars($message) . '</p>';
        $html .= '</section>';

        return $html;
    }
}
