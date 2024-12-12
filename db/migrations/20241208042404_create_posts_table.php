<?php

use Phinx\Migration\AbstractMigration;

class CreatePostsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('posts');
        $table->addColumn('title', 'string', ['limit' => 255])
              ->addColumn('content', 'text', ['null' => true])
              ->addColumn('slug', 'string', ['limit' => 255])
              ->addColumn('is_draft', 'boolean', ['default' => true]) // 下書きデフォルト
              ->addColumn('published_at', 'timestamp', ['null' => true, 'default' => null]) // 公開日
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
              ->addIndex(['slug'], ['unique' => true]) // スラッグを一意に
              ->create();
    }
}
