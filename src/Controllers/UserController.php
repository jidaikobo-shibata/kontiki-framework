<?php

namespace jidaikobo\kontiki\Controllers;

use jidaikobo\kontiki\Models\User;
use jidaikobo\kontiki\Services\SidebarService;
use jidaikobo\kontiki\Utils\Lang;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

class UserController extends BaseController
{
    private User $userModel;

    public function __construct(User $userModel, PhpRenderer $view, SidebarService $sidebarService)
    {
        parent::__construct($view, $sidebarService);
        $this->userModel = $userModel;
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
    public function createForm(Request $request, Response $response): Response
    {
        return $this->view->render(
            $response,
            'users/create.php',
            [
            'pageTitle' => 'Create New User',
            ]
        );
    }

    /**
     * Store a new user in the database.
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        try {
            $this->userModel->create($data);
            return $response
                ->withHeader('Location', '/admin/users')
                ->withStatus(302);
        } catch (\InvalidArgumentException $e) {
            return $this->view->render(
                $response,
                'users/create.php',
                [
                'pageTitle' => 'Create New User',
                'error' => $e->getMessage(),
                'oldInput' => $data,
                ]
            );
        }
    }

    /**
     * Display the form for editing a specific user.
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  array    $args
     * @return Response
     */
    public function editForm(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $user = $this->userModel->getById($id);

        if (!$user) {
            return $this->view->render(
                $response,
                'errors/404.php',
                [
                'pageTitle' => 'User Not Found',
                ]
            )->withStatus(404);
        }

        return $this->view->render(
            $response,
            'users/edit.php',
            [
            'pageTitle' => 'Edit User',
            'user' => $user,
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
