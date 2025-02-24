<?php

use Phinx\Migration\AbstractMigration;

class CreateTermMetaTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table(
            'term_meta',
            [
                'id' => false,
                'primary_key' => ['term_id', 'meta_key']
            ]
        );

        // Define columns
        $table->addColumn('term_id', 'integer', ['null' => false, 'comment' => 'ID of the term'])
              ->addColumn('meta_key', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('value', 'text', ['null' => true]);

        // Add indexes
        $table->addIndex(['term_id', 'meta_key'], [
            'unique' => true,
            'name' => 'idx_termid_metakey'
        ]);

        // Create the table
        $table->create();
    }
}
