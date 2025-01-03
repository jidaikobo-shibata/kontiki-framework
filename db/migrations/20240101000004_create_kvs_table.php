<?php

use Phinx\Migration\AbstractMigration;

final class CreateKvsTable extends AbstractMigration
{
    public function change(): void
    {
        // Create the kvs_store table
        $table = $this->table('kvs_store', ['id' => true, 'primary_key' => ['id']]);

        // Define columns
        $table->addColumn('model', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('model_id', 'integer', ['null' => false, 'comment' => 'ID of the model'])
              ->addColumn('key', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('value', 'text', ['null' => true])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'null' => false])
              ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP', 'null' => false]);

        // Add indexes
        $table->addIndex(['model', 'model_id', 'key'], [
            'unique' => true,
            'name' => 'idx_model_modelid_key'
        ]);

        // Create the table
        $table->create();
    }
}
