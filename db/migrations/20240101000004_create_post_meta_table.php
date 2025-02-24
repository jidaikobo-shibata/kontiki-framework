<?php

use Phinx\Migration\AbstractMigration;

final class CreatePostMetaTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('post_meta', ['id' => true, 'primary_key' => ['id']]);

        // Define columns
        $table->addColumn('model', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('model_id', 'integer', ['null' => false, 'comment' => 'ID of the model'])
              ->addColumn('meta_key', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('meta_value', 'text', ['null' => true]);

        // Add indexes
        $table->addIndex(['model', 'model_id', 'meta_key'], [
            'unique' => true,
            'name' => 'idx_model_modelid_metakey'
        ]);

        // Create the table
        $table->create();
    }
}
