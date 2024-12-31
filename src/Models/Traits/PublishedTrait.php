<?php

namespace Jidaikobo\Kontiki\Models\Traits;

use Illuminate\Database\Query\Builder;

trait PublishedTrait
{
    protected string $publishedField = 'published_at';

    public function applyPublisedConditions(Builder $query): Builder
    {
        $currentTime = date('Y-m-d H:i:s');
        return $query->where($this->publishedField, '<=', $currentTime);
    }

    public function applyNotPublisedConditions(Builder $query): Builder
    {
        $currentTime = date('Y-m-d H:i:s');
        return $query->where($this->publishedField, '>=', $currentTime);
    }
}
