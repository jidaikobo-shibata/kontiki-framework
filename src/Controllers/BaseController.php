<?php

namespace jidaikobo\kontiki\Controllers;

use Aura\Session\Session;
use jidaikobo\kontiki\Services\SidebarService;
use jidaikobo\kontiki\Utils\Env;
use jidaikobo\kontiki\Utils\Lang;
use jidaikobo\kontiki\Utils\FormHandler;
use jidaikobo\kontiki\Utils\FormRenderer;
use jidaikobo\kontiki\Utils\TableHandler;
use jidaikobo\kontiki\Utils\TableRenderer;
use PDO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use Slim\Views\PhpRenderer;

abstract class BaseController
{
    protected PhpRenderer $view;
    protected SidebarService $sidebarService;
    protected Session $session;
    protected string $model;

    public function __construct(PhpRenderer $view, SidebarService $sidebarService, Session $session, PDO $pdo)
    {
        $this->view = $view;
        $this->view->setAttributes(['sidebarItems' => $sidebarService->getLinks()]);
        $this->sidebarService = $sidebarService;
        $this->session = $session;
        $this->pdo = $pdo;
    }

    /**
     * Create a redirect response.
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $target      Route name or URL.
     * @param  array    $routeData   Route parameters (for named routes).
     * @param  int      $status      HTTP status code for the redirect (default: 302).
     * @return Response
     */
    protected function redirect(Request $request, Response $response, string $target, array $routeData = [], int $status = 302): Response
    {
        if (strpos($target, '/') === 0 || filter_var($target, FILTER_VALIDATE_URL)) {
            $redirectUrl = $target;
        } else {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $redirectUrl = $routeParser->urlFor($target, $routeData);
        }

        return $response
            ->withHeader('Location', Env::get('BASEPATH') . $redirectUrl)
            ->withStatus($status);
    }

    protected function getCsrfToken(): string
    {
        return $this->session->getCsrfToken()->getValue();
    }

    protected function validateCsrfToken(?string $token): bool
    {
        return $this->session->getCsrfToken()->isValid($token);
    }

    protected function setFlashMessage(string $type, string $message): void
    {
        // セッションセグメントを取得
        $segment = $this->session->getSegment('jidaikobo\kontiki\flash');

        // 指定されたタイプ（例: success, error）にメッセージを追加
        $existingMessages = $segment->get($type, []);
        $existingMessages[] = $message;
        $segment->set($type, $existingMessages);
    }

    protected function setFlashErrors(array $errors): void
    {
        $segment = $this->session->getSegment('jidaikobo\kontiki\flash');

        // 既存のエラーを取得し、新しいエラーをマージ
        $existingErrors = $segment->get('errors', []);
        $mergedErrors = array_merge_recursive($existingErrors, $errors);

        $segment->set('errors', $mergedErrors);
    }

    protected function getFlashMessages(string $type, array $default = []): array
    {
        $segment = $this->session->getSegment('jidaikobo\kontiki\flash');
        $messages = $segment->get($type, $default);
        if (empty($messages)) {
          $messages = $default;
        }
        $segment->set($type, []);
        return $messages;
    }

    /**
     * モデルインスタンスを取得
     */
    protected function getModelInstance(): object
    {
        if (!class_exists($this->model)) {
            throw new \RuntimeException("Model class {$this->model} not found.");
        }

        return new $this->model($this->pdo);
    }

    /**
     * 一覧表示の汎用メソッド
     */
    public function index(Request $request, Response $response): Response
    {
        $model = $this->getModelInstance();
        $error = $this->getFlashMessages('errors', []);
        $success = $this->getFlashMessages('success', []);
        $data = $model->getAll();

        $tableRenderer = new TableRenderer($model, $data, $this->view);
        $content = $tableRenderer->render();

        $tableHandler = new TableHandler();

        if (!empty($error)) {
            $content = $tableHandler->addErrors($content, $error);
        }

        if (!empty($success)) {
            $content = $tableHandler->addSuccessMessages($content, $success);
        }

        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => Lang::get("{$this->table}_management", ucfirst($this->table) . ' Management'),
                'content' => $content,
            ]
        );
    }

    public function create(Request $request, Response $response): Response
    {
        $model = $this->getModelInstance();
        $data = [];

        return $this->renderForm(
            $response,
            "/admin/{$this->table}/create",
            Lang::get("{$this->table}_create", 'Create ' . ucfirst($this->table)),
            $model->getFieldDefinitionsWithDefaults($data)
        );
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $model = $this->getModelInstance();
        $data = $this->getFlashMessages('data', $model->getById($id));

        if (!$data) {
            return $this->redirect($request, $response, "/admin/{$this->table}/index");
        }

        return $this->renderForm(
            $response,
            "/admin/{$this->table}/edit/{$id}",
            Lang::get("{$this->table}_edit", 'Edit ' . ucfirst($this->table)),
            $model->getFieldDefinitionsWithDefaults($data)
        );
    }

    protected function renderForm(
        Response $response,
        string $action,
        string $title,
        array $fields
    ): Response {
        $segment = $this->session->getSegment('jidaikobo\kontiki\flash');
        $error = $this->getFlashMessages('errors', []);
        $success = $this->getFlashMessages('success', []);

        $formRenderer = new FormRenderer($fields, $this->view);
        $formHtml = $formRenderer->render();

        $content = $this->view->fetch(
            'forms/edit.php',
            [
                'actionAttribute' => Env::get('BASEPATH') . $action,
                'csrfToken' => $this->getCsrfToken(),
                'formHtml' => $formHtml,
            ]
        );

        $formHandler = new FormHandler($content, $this->getModelInstance());

        if (!empty($error)) {
            $formHandler->addErrors($error);
        }

        if (!empty($success)) {
            $formHandler->addSuccessMessages($success);
        }
        $content = $formHandler->getHtml();

        return $this->view->render(
            $response,
            'layout.php',
            [
                'pageTitle' => $title,
                'content' => $content,
            ]
        );
    }

    public function handleCreate(Request $request, Response $response): Response
    {
        return $this->handleSave($request, $response, 'create');
    }

    public function handleEdit(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        return $this->handleSave($request, $response, 'edit', $id);
    }

    protected function handleSave(Request $request, Response $response, string $actionType, ?int $id = null): Response
    {
        $model = $this->getModelInstance();
        $data = $request->getParsedBody();

        $redirectTo = $actionType === 'create' ? "/admin/{$this->table}/create" : "/admin/{$this->table}/edit/{$id}";

        // CSRFトークン検証
        if (empty($data['_csrf_value']) || !$this->validateCsrfToken($data['_csrf_value'])) {
            $this->setFlashErrors([Lang::get("csrf_invalid", 'Invalid CSRF token.')]);
            return $this->redirect($request, $response, $redirectTo);
        }

        // バリデーション
        $validationResult = $model->validate($data);
        if (!$validationResult['valid']) {
            $this->setFlashErrors($validationResult['errors']);
            return $this->redirect($request, $response, $redirectTo);
        }

        // 保存または更新
        try {
            if ($actionType === 'create') {
                $model->create($data);
                $lastInsertId = $model->getLastInsertId();
                $redirectTo = "/admin/{$this->table}/edit/{$lastInsertId}";
            } elseif ($actionType === 'edit' && $id !== null) {
                $model->update($id, $data);
                $redirectTo = "/admin/{$this->table}/edit/{$id}";
            }

            $this->setFlashMessage('success', Lang::get("{$this->table}_save_success", 'Saved successfully.'));
            return $this->redirect($request, $response, $redirectTo);
        } catch (\Exception $e) {
            $this->setFlashErrors([[$e->getMessage()]]);
            $redirectTo = $actionType === 'create' ? "/admin/{$this->table}/create" : "/admin/{$this->table}/edit/{$id}";
            return $this->redirect($request, $response, $redirectTo);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $model = $this->getModelInstance();

        // 対象データを取得
        $data = $model->getById($id);

        if (!$data) {
            return $this->redirect($request, $response, "/admin/{$this->table}/index");
        }

        // 全フィールドをreadonlyに設定
        $fields = $model->getFieldDefinitionsWithDefaults($data);
        foreach ($fields as &$field) {
            $field['attributes']['readonly'] = 'readonly';
        }

        return $this->renderForm(
            $response,
            "/admin/{$this->table}/delete/{$id}",
            Lang::get("{$this->table}_edit", 'Delete ' . ucfirst($this->table)),
            $fields
        );
    }

    public function handleDelete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $model = $this->getModelInstance();
        $data = $request->getParsedBody();

        // CSRFトークン検証
        if (empty($data['_csrf_value']) || !$this->validateCsrfToken($data['_csrf_value'])) {
            return $response->withStatus(400)->write('Invalid CSRF token.');
        }

        // データ削除
        try {
            if ($model->delete($id)) {
                $this->setFlashMessage('success', Lang::get("{$this->table}_delete_success", ucfirst($this->table) . " deleted successfully."));
                return $this->redirect($request, $response, "/admin/{$this->table}/index");
            }
        } catch (\Exception $e) {
            $this->setFlashErrors([Lang::get("{$this->table}_delete_failed", "Failed to delete " . ucfirst($this->table) . ".")]);
        }

        $redirectTo = "/admin/{$this->table}/edit/{$id}";
        return $this->redirect($request, $response, $redirectTo);
    }

}
