<?php

namespace Jidaikobo\Kontiki\Models\Traits;

trait PostMetaTrait
{
    public function getAllPostMeta(int $id): array
    {
        $modelClass = static::class;
        $result = $this->db->table('post_meta')
            ->where('model', $modelClass)
            ->where('model_id', $id)
            ->get()
            ->map(fn($item) => [
                'meta_key' => $item->meta_key,
                'meta_value' => json_decode($item->meta_value, true)
            ])
            ->toArray();

        $retvals = [];
        foreach ($result as $each)
        {
            $retvals[$each['meta_key']] = $each['meta_value'];
        }

        return $retvals;
    }

    public function getPostMeta(int $id, string $key): mixed
    {
        $modelClass = static::class;
        $result = $this->db->table('post_meta')
            ->where('model', $modelClass)
            ->where('model_id', $id)
            ->where('meta_key', $key)
            ->first();

        if ($result) {
            $result = (array) $result;
            return json_decode($result['meta_value'], true);
        }

        return null;
    }

    public function createPostMeta(int $id, string $key, mixed $value): void
    {
        $modelClass = static::class;
        $data = [
            'model' => $modelClass,
            'model_id' => $id,
            'meta_key' => $key,
            'meta_value' => json_encode($value),
        ];
        $this->db->table('post_meta')->insert($data);
    }

    public function updatePostMeta(int $id, string $key, mixed $value): void
    {
        $data = [
            'meta_value' => json_encode($value),
        ];

        $modelClass = static::class;
        $this->db->table('post_meta')
            ->where('model', $modelClass)
            ->where('model_id', $id)
            ->where('meta_key', $key)
            ->update($data);
    }

    public function deletePostMeta(int $id, string $key): mixed
    {
        $modelClass = static::class;
        $this->db->table('post_meta')
            ->where('model', $modelClass)
            ->where('model_id', $id)
            ->where('meta_key', $key)
            ->delete();
    }
}
