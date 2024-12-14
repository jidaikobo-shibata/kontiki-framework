<?php

namespace jidaikobo\kontiki\Controllers;

use Aura\Session\Session;
use jidaikobo\kontiki\Models\User;
use jidaikobo\kontiki\Services\SidebarService;
use jidaikobo\kontiki\Utils\Lang;
use jidaikobo\kontiki\Utils\FormHandler;
use jidaikobo\kontiki\Utils\FormRenderer;
use jidaikobo\Log;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class UserController extends BaseController
{
    private Session $session;
    protected PhpRenderer $view;
    private User $userModel;

    public function __construct(User $userModel, PhpRenderer $view, SidebarService $sidebarService, Session $session)
    {
        parent::__construct($view, $sidebarService);
        $this->userModel = $userModel;
        $this->session = $session;
    }

    /**
     * Display a list of users.
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        $users = $this->userModel->getAll();

        $content = $this->view->fetch(
            'users/index.php',
            [
            'users' => $users,
            ]
        );

        return $this->view->render(
            $response,
            'layout.php',
            [
            'pageTitle' => Lang::get('users_management', 'Users Management'),
            'content' => $content
            ]
        );
    }

    /**
     * Display the form for creating a new user.
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $segment = $this->session->getSegment('jidaikobo\kontiki\flash');
        $error = $segment->get('error', []);
        $data = $segment->get('data', []);

        $segment->clear();

        $fields = $this->userModel->getFieldDefinitionsWithDefaults($data);

        $formRenderer = new FormRenderer($fields, $this->view);
        $formHtml = $formRenderer->render();

        $content = $this->view->fetch(
            'forms/edit.php',
            [
                'actionAttribute' => './save',
                'csrfToken' => $this->session->getCsrfToken(),
                'formHtml' => $formHtml
            ]
        );

        if (!empty($error)) {
            $formHandler = new FormHandler($content);
            $formHandler->addErrors($error);
            $content = $formHandler->getHtml();
        }

        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => Lang::get('create_new_user', 'Create New User'),
                'content' => $content
            ]
        );
    }

    public function save(Request $request, Response $response): Response
    {
        $csrfToken = $this->session->getCsrfToken();
        $segment = $this->session->getSegment('jidaikobo\kontiki\flash');

        $data = $request->getParsedBody();

        if (!isset($data['_csrf_value']) || !$csrfToken->isValid($data['_csrf_value'])) {
            $response->getBody()->write('Invalid CSRF token.');
            return $response->withStatus(400);
        }

        $validationResult = $this->userModel->validate($data);
        if (!$validationResult['valid']) {
            $segment->set('error', $validationResult['errors']);
            $segment->set('data', $data);
            return $this->redirect($request, $response, 'add_new_user');
        }

        // save
        try {
            if ($this->userModel->create($data)) {
                $segment->set('success', Lang::get('success', 'Success'));
                $id = $this->userModel->getLastInsertId();
                $redirect = $this->redirect($request, $response, '.edit_user', ['id' => $id]);
            }
        } catch (\RuntimeException $e) {
            $segment->set(
                'error',
                [[$e->getMessage()]]
            );
            $segment->set('data', $data);
            $redirect = $this->redirect($request, $response, 'add_new_user');
        }
        return $redirect;
    }

    /**
     * Display the form for editing a specific user.
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $args
     * @return Response
     */
    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];

        $segment = $this->session->getSegment('jidaikobo\kontiki\flash');
        $error = $segment->get('error', []);
        $data = $segment->get('data', $this->userModel->getById($id));

        $segment->clear();

        if (!$data) {
            return $redirect = $this->redirect($request, $response, 'add_new_user');
        }

        $fields = $this->userModel->getFieldDefinitionsWithDefaults($data);

        $formRenderer = new FormRenderer($fields, $this->view);
        $formHtml = $formRenderer->render();

        $content = $this->view->fetch(
            'forms/edit.php',
            [
                'actionAttribute' => './update',
                'csrfToken' => $this->session->getCsrfToken(),
                'formHtml' => $formHtml
            ]
        );

        if (!empty($error)) {
            $formHandler = new FormHandler($content);
            $formHandler->addErrors($error);
            $content = $formHandler->getHtml();
        }

        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => Lang::get('edit_user', 'Edit User'),
                'content' => $content
            ]
        );
    }

    /**
     * Update a specific user in the database.
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $args
     * @return Response
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $data = $request->getParsedBody();

        try {
            $this->userModel->update($id, $data);
            return $response
                ->withHeader('Location', '/admin/users')
                ->withStatus(302);
        } catch (\InvalidArgumentException $e) {
            $user = $this->userModel->getById($id);

            return $this->view->render(
                $response,
                'users/edit.php',
                [
                'pageTitle' => 'Edit User',
                'user' => $user,
                'error' => $e->getMessage(),
                'oldInput' => $data,
                ]
            );
        }
    }

    /**
     * Delete a specific user from the database.
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $args
     * @return Response
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];

        if ($this->userModel->delete($id)) {
            return $response
                ->withHeader('Location', '/admin/users')
                ->withStatus(302);
        }

        return $this->view->render(
            $response,
            'users/index.php',
            [
            'pageTitle' => 'User Management',
            'error' => 'Failed to delete user.',
            'users' => $this->userModel->getAll(),
            ]
        );
    }
}
