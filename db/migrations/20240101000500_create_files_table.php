<?php

use Phinx\Migration\AbstractMigration;

class CreateFilesTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('files');
        $table->addColumn('path', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('description', 'text', ['null' => true])
              ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn(
                  'updated_at',
                  'timestamp',
                  ['default' => 'CURRENT_TIMESTAMP', 'update' => 'CURRENT_TIMESTAMP']
              )
              ->addIndex(['path'], ['unique' => true, 'name' => 'idx_unique_path'])
              ->create();
    }
}
