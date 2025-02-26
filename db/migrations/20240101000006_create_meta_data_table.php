<?php

use Phinx\Migration\AbstractMigration;

final class CreateMetaDataTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table(
            'meta_data',
            [
                'id' => false,
                'primary_key' => ['target', 'target_id', 'meta_key']
            ]
        );

        // Define columns
        $table->addColumn('target', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('target_id', 'integer', ['null' => false])
              ->addColumn('meta_key', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('meta_value', 'text', ['null' => true]);

        // Add indexes
        $table->addIndex(['target', 'target_id', 'meta_key'], [
            'name' => 'idx_target_targetid_metakey'
        ]);

        // Create the table
        $table->create();
    }
}
