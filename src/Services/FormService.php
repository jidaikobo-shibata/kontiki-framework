<?php

namespace Jidaikobo\Kontiki\Services;

use Jidaikobo\Kontiki\Models\ModelInterface;
use Jidaikobo\Kontiki\Utils\Env;
use Jidaikobo\Kontiki\Utils\FormRenderer;
use Jidaikobo\Kontiki\Utils\FormHandler;
use Jidaikobo\Kontiki\Utils\FlashManager;
use Jidaikobo\Kontiki\Utils\CsrfManager;
use Slim\Views\PhpRenderer;

/**
 * FormService
 *
 * A service class to handle form rendering and processing.
 */
class FormService
{
    private CsrfManager $csrfManager;
    private FlashManager $flashManager;
    private ModelInterface $model;
    private PhpRenderer $view;

    /**
     * Constructor
     *
     * @param PhpRenderer     $view         The view renderer.
     * @param ModelInterface  $model        The associated model.
     * @param FlashManager    $flashManager Handles flash messages.
     * @param CsrfManager     $csrfManager  Manages CSRF tokens.
     */
    public function __construct(
        PhpRenderer $view,
        ModelInterface $model,
        FlashManager $flashManager,
        CsrfManager $csrfManager
    ) {
        $this->view = $view;
        $this->model = $model;
        $this->flashManager = $flashManager;
        $this->csrfManager = $csrfManager;
    }

    /**
     * Generate form HTML without additional processing.
     *
     * @param string $action       The form action URL.
     * @param array  $fields       The form fields definitions.
     * @param string $description  An optional description for the form.
     * @param string $buttonText   The text to display on the submit button.
     *
     * @return string The generated HTML for the form.
     */
    public function formHtml(
        string $action,
        array $fields,
        string $description = '',
        string $buttonText = 'Submit'
    ): string {
        $formRenderer = new FormRenderer($fields, $this->view);

        return $this->view->fetch(
            'forms/edit.php',
            [
                'actionAttribute' => Env::get('BASEPATH') . $action,
                'csrfToken' => $this->csrfManager->getToken(),
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
    public function processFormHtml(string $formHtml): string
    {
        $formHandler = new FormHandler($formHtml, $this->model);
        $formHandler->addErrors($this->flashManager->getData('errors', []));
        $formHandler->addSuccessMessages($this->flashManager->getData('success', []));

        return $formHandler->getHtml();
    }
}
