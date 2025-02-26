<?php

namespace Jidaikobo\Kontiki\Controllers;

use Aura\Session\Session;
use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Models\CategoryModel;
use Jidaikobo\Kontiki\Services\GetRoutesService;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\PhpRenderer;

class CategoryController extends BaseController
{
    use Traits\IndexTrait;
    use Traits\IndexTaxonomyTrait;
    use Traits\CreateEditTrait;
    use Traits\DeleteTrait;

    protected string $adminDirName = 'post/category';
    protected string $label = 'Post/Category';
    protected CategoryModel $model;

    protected function setModel(): void
    {
        $db = Database::getInstance()->getConnection();
        $this->model = new CategoryModel($db);
    }
}
