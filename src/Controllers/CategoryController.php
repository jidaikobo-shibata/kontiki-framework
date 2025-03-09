<?php

namespace Jidaikobo\Kontiki\Controllers;

use Jidaikobo\Kontiki\Models\CategoryModel;

class CategoryController extends BaseController
{
    // use Traits\IndexTrait;
    // use Traits\IndexTaxonomyTrait;
    // use Traits\CreateEditTrait;
    // use Traits\DeleteTrait;

    protected string $adminDirName = 'post/category';
    protected string $label = 'Post/Category';
    protected CategoryModel $model;

    protected function setModel(): void
    {
        $this->model = new CategoryModel();
    }
}
