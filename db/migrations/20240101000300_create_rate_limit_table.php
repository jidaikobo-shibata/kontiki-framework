<?php

use Phinx\Migration\AbstractMigration;

final class CreateRateLimitTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table(
            'rate_limit',
            ['id' => false, 'primary_key' => ['ip_address']]
        );

        $table->addColumn('ip_address', 'string', ['limit' => 45])
              ->addColumn('attempt_count', 'integer', ['default' => 0])
              ->addColumn('first_attempt', 'integer', ['null' => true, 'default' => null])
              ->addColumn('last_attempt', 'integer')
              ->addColumn('blocked_until', 'integer', ['null' => true, 'default' => null])
              ->addIndex(['ip_address'], ['unique' => true])
              ->create();
    }
}
