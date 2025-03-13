<?php

use Jidaikobo\Kontiki\Core\Database;
use Jidaikobo\Kontiki\Core\Auth;
use Jidaikobo\Kontiki\Models\UserModel;
use Jidaikobo\Kontiki\Models\PostModel;

if (!function_exists('getIndex')) {
    /**
     * @param array $args Configuration for the request.
     * @param string $env environment.
     * @return array
     */
    function getIndex(array $args, string $env = 'production'): array
    {
        $app = Jidaikobo\Kontiki\Bootstrap::init($env);
        $container = $app->getContainer();
        $userModel = new UserModel(
            $container->get(Database::class)
        );
        $model = new PostModel(
            $container->get(Database::class),
            $container->get(Auth::class),
            $userModel
        );
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
        $container = $app->getContainer();
        $userModel = new UserModel(
            $container->get(Database::class)
        );
        $model = new PostModel(
            $container->get(Database::class),
            $container->get(Auth::class),
            $userModel
        );
        $slug = $args['slug'] ?? '';
        $retval = [];
        $retval['body'] = $model->getByFieldWithCondtioned('slug', $slug, 'published');
        return $retval;
    }
}
