<?php

use Phinx\Migration\AbstractMigration;

class CreateTermsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('terms');
        $table->addColumn('taxonomy', 'string', ['limit' => 32])
              ->addColumn('name', 'string', ['limit' => 255])
              ->addColumn('slug', 'string', ['limit' => 255])
              ->addColumn('parent_id', 'integer', ['default' => 0])
              ->addColumn('term_order', 'integer', ['default' => 0])
              ->addIndex(['taxonomy', 'slug'], ['unique' => true])
              ->create();
    }
}
