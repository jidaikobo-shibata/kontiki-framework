<?php

namespace Jidaikobo\Kontiki\Frontend;

class Client
{
    public static function getItems(array $args) :array
    {
        $postType = $args['postType'] ?? 'post';
        $postType = $args['post_type'] ?? $postType;

        $db = Database::getInstance()->getConnection();
        $this->model = new PostModel($db, $this->app->getContainer()->get(AuthService::class));

    }

}
