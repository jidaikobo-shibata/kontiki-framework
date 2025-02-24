<?php

use Phinx\Migration\AbstractMigration;

class CreateTermsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table(
            'terms',
            [
                'id' => false,
                'primary_key' => ['term_id']
            ]
        );
        $table->addColumn('term_id', 'integer', ['identity' => true])
              ->addColumn('name', 'string', ['limit' => 255])
              ->addColumn('slug', 'string', ['limit' => 255]);

        $table->addIndex(['term_id', 'slug'], [
            'unique' => true,
            'name' => 'idx_termid_slug'
        ]);

        $table->create();
    }
}
