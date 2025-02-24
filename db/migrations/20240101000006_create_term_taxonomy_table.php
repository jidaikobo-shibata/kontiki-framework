<?php

use Phinx\Migration\AbstractMigration;

class CreateTermTaxonomyTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table(
            'term_taxonomy',
            [
                'id' => false,
                'primary_key' => ['term_taxonomy_id']
            ]
        );
        $table->addColumn('term_taxonomy_id', 'integer', ['identity' => true])
              ->addColumn('term_id', 'integer')
              ->addColumn('taxonomy', 'string', ['limit' => 32])
              ->addColumn('parent', 'integer', ['default' => 0])
              ->addColumn('term_order', 'integer', ['default' => 0])
              ->addIndex(['term_id', 'taxonomy'], ['unique' => true])
              ->create();
    }
}
