<?php

namespace Jidaikobo\Kontiki\Controllers;

use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Models\PostModel;
use Slim\App;

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
    protected PostModel $model;

    protected function setModel(): void
    {
        $this->model = new PostModel();
    }
}
