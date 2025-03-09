<?php

if (!function_exists('getIndex')) {
    /**
     * @param array $args Configuration for the request.
     * @return array|null The API response decoded as an array, or null on failure.
     */
    function getIndex(array $args): ?array
    {
        $model = new \Jidaikobo\Kontiki\Models\PostModel();
        $retval = [];
        $retval['body'] = $model->getIndexData('published', $args);
        $retval['pagination'] = $model->getPagination();
        return $retval;
    }
}

if (!function_exists('getData')) {
    /**
     * @param array $args Configuration for the request.
     * @return array|null The API response decoded as an array, or null on failure.
     */
    function getData(array $args): ?array
    {
        $model = new \Jidaikobo\Kontiki\Models\PostModel();
        $slug = $args['slug'] ?? '';
        $retval = [];
        $retval['body'] = $model->getByFieldWithCondtioned('slug', $slug, 'published');
        return $retval;
    }
}
