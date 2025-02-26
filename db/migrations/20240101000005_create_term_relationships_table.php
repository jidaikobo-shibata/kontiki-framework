<?php

use Phinx\Migration\AbstractMigration;

class CreateTermRelationshipsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table(
            'term_relationships',
            [
                'id' => false,
                'primary_key' => ['post_id', 'term_id']
            ]
        );
        $table->addColumn('post_id', 'integer')
              ->addColumn('term_id', 'integer')
              ->create();
    }
}
