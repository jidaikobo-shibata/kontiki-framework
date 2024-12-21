<?php

use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
    public function run() : void
    {
        // Fakerインスタンスを作成
        $faker = \Faker\Factory::create();

        // 挿入するデータを準備
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'username' => $faker->userName,
                'password' => password_hash('hitsuji#HANAZAME', PASSWORD_BCRYPT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        // テーブルにデータを挿入
        $this->table('users')->insert($data)->saveData();
    }
}
