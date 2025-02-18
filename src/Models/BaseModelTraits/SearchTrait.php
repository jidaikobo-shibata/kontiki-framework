<?php

namespace Jidaikobo\Kontiki\Models\BaseModelTraits;

use Illuminate\Database\Query\Builder;

trait SearchTrait
{
    /**
     * Get searchable fields from the model's properties.
     *
     * @return array
     */
    protected function getSearchableFields(): array
    {
        $searchableColumns = [];
        foreach ($this->getFieldDefinitions() as $column => $config) {
            if (isset($config['searchable']) && $config['searchable'] === true) {
                $searchableColumns[] = $column;
            }
        }
        return $searchableColumns;
    }

    public function getAdditionalConditions(Builder $query, string $context = 'all' ): Builder
    {
        return $query;
    }

    public function buildSearchConditions(string $keyword = ''): Builder
    {
        $query = $this->db->table($this->table);

        // キーワード条件
        if (!empty($keyword)) {
            $searchableColumns = $this->getSearchableFields();

            $query->where(function ($q) use ($keyword, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'LIKE', "%{$keyword}%");
                }
            });
        }

        return $query;
    }
}
