<?php

use Phinx\Migration\AbstractMigration;
use Jidaikobo\Kontiki\Database\TableSchema;

class CreatePostsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('posts', ['id' => true, 'primary_key' => ['id']]);
        TableSchema::applyPostSchema($table);
        $table->create();
    }
}
