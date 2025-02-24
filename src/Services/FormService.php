<?php

namespace Jidaikobo\Kontiki\Services;

use Jidaikobo\Kontiki\Models\ModelInterface;
use Jidaikobo\Kontiki\Renderers\FormRenderer;
use Jidaikobo\Kontiki\Handlers\FormHandler;
use Slim\Views\PhpRenderer;

/**
 * FormService
 *
 * A service class to handle form rendering and processing.
 */
class FormService
{
    private ModelInterface $model;
    private PhpRenderer $view;

    /**
     * Constructor
     *
     * @param PhpRenderer     $view  The view renderer.
     * @param ModelInterface  $model The associated model.
     */
    public function __construct(
        PhpRenderer $view,
        ModelInterface $model
    ) {
        $this->view = $view;
        $this->model = $model;
    }

    /**
     * Generate form HTML without additional processing.
     *
     * @param string $action       The form action URL.
     * @param array  $fields       The form fields definitions.
     * @param string $csrfToken    CSRF Token.
     * @param string $description  An optional description for the form.
     * @param string $buttonText   The text to display on the submit button.
     *
     * @return string The generated HTML for the form.
     */
    public function formHtml(
        string $action,
        array $fields,
        string $csrfToken,
        string $description = '',
        string $buttonText = 'Submit'
    ): string {
//$this->model

        $formRenderer = new FormRenderer($fields, $this->view);

        return $this->view->fetch(
            'forms/edit.php',
            [
                'actionAttribute' => env('BASEPATH', '') . $action,
                'csrfToken' => $csrfToken,
                'formHtml' => $formRenderer->render(),
                'description' => $description,
                'buttonText' => $buttonText,
            ]
        );
    }

    /**
     * Process form HTML by adding errors and success messages.
     *
     * @param string $formHtml The raw form HTML to process.
     *
     * @return string The processed HTML with errors and success messages.
     */
    public function addMessages(string $formHtml, array $errors, array $success = array()): string
    {
        $formHandler = new FormHandler($formHtml, $this->model);
        $formHandler->addErrors($errors);
        $formHandler->addSuccessMessages($success);

        return $formHandler->getHtml();
    }
}
