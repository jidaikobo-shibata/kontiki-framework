<?php

use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Core\Auth;
use Jidaikobo\Kontiki\Models\UserModel;
use Jidaikobo\Kontiki\Models\PostModel;
use Jidaikobo\Kontiki\Services\ValidationService;

if (!function_exists('getIndex')) {
    /**
     * @param array $args Configuration for the request.
     * @param string $env environment.
     * @return array
     */
    function getIndex(array $args, string $env = 'production'): array
    {
        $app = Jidaikobo\Kontiki\Bootstrap::init($env);
        $model = $app->getContainer()->get(PostModel::class);
        $retval = [];
        $retval['body'] = $model->getIndexData('published', $args);
        $retval['pagination'] = $model->getPagination();
        return $retval;
    }
}

if (!function_exists('getData')) {
    /**
     * @param array $args Configuration for the request.
     * @param string $env environment.
     * @return array
     */
    function getData(array $args, string $env = 'production'): array
    {
        $app = Jidaikobo\Kontiki\Bootstrap::init($env);
        $model = $app->getContainer()->get(PostModel::class);
        $slug = $args['slug'] ?? '';
        $retval = [];
        $retval['body'] = $model->getByFieldWithCondtioned('slug', $slug, 'published');
        return $retval;
    }
}

if (!function_exists('printEditDataLink')) {
    /**
     * @param array $args Configuration for the request.
     * @param string $env environment.
     * @return void
     */
    function printEditDataLink(array $args, string $env = 'production'): void
    {
        $app = Jidaikobo\Kontiki\Bootstrap::init($env);
        $model = $app->getContainer()->get(PostModel::class);
        $slug = $args['slug'] ?? '';
        $retval = [];
        $data = $model->getByFieldWithCondtioned('slug', $slug, 'published');
        $url = $app->getBasePath() . '/' . e($data['post_type']) . '/edit/' . e($data['id']);

        $html = '';
        $html .= '<p class="edit-this-page"><a href="' . $url . '">';
        $html .= __('edit_this_content');
        $html .= '</a></a>';
        echo $html;
    }
}
