<?php

use Phinx\Migration\AbstractMigration;

class CreatePostsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('posts', ['id' => true, 'primary_key' => ['id']]);
        $table->addColumn('post_type', 'string', ['limit' => 50])
              ->addColumn('title', 'string', ['limit' => 255])
              ->addColumn('content', 'text', ['null' => true])
              ->addColumn('slug', 'string', ['limit' => 255])
              ->addColumn('parent_id', 'integer', ['null' => true, 'default' => null])
              ->addColumn('status', 'string', ['limit' => 50, 'default' => 'draft'])
              ->addColumn('sort_order', 'integer', ['default' => 1])
              ->addColumn('creator_id', 'integer', ['default' => 1])
              ->addColumn('published_at', 'timestamp', ['null' => true, 'default' => null])
              ->addColumn('expired_at', 'timestamp', ['null' => true, 'default' => null])
              ->addColumn('deleted_at', 'timestamp', ['null' => true, 'default' => null])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP'])
              ->addIndex(['post_type', 'slug'], ['unique' => true])
              ->addIndex(['sort_order'])
              ->create();
    }
}
