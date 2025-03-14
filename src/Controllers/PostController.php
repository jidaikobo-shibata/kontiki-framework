<?php

namespace Jidaikobo\Kontiki\Controllers;

use Slim\App;

use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Models\PostModel;
use Jidaikobo\Kontiki\Services\FormService;
use Jidaikobo\Kontiki\Services\TableService;

class PostController extends BaseController
{
    use Traits\IndexTrait;
    use Traits\IndexAllTrait;
    use Traits\IndexPublishedTrait;
    use Traits\IndexDraftTrait;
    use Traits\IndexReservedTrait;
    use Traits\IndexExpiredTrait;
    use Traits\CreateEditTrait;
    use Traits\TrashRestoreTrait;
    use Traits\DeleteTrait;
    use Traits\MarkdownHelpTrait;
    use Traits\PreviewTrait;

    protected string $adminDirName = 'post';
    protected string $label = 'Post';

    private PostModel $model;
    private FormService $formService;
    private TableService $tableService;

    public function __construct(
        App $app,
        FormService $formService,
        TableService $tableService,
        PostModel $model
    ) {
        parent::__construct($app);
        $this->formService = $formService;
        $this->formService->setModel($model);
        $this->tableService = $tableService;
        $this->tableService->setModel($model);
        $this->model = $model;
    }
}
