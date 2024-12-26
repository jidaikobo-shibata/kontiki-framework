<?php

use Phinx\Seed\AbstractSeed;

class PostsSeeder extends AbstractSeed
{
    public function run() : void
    {
        // Fakerインスタンスを作成
        $faker = \Faker\Factory::create();

        // 挿入するデータを準備
        $data = [];
        for ($i = 0; $i < 10; $i++) {
            $data[] = [
                'title' => $faker->sentence,
                'content' => $faker->paragraph,
                'slug' => $faker->slug,
                'is_draft' => $faker->boolean,
                'creator_id' => 1,
                'published_at' => $publishedAt ? $publishedAt->format('Y-m-d H:i:s') : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        // テーブルにデータを挿入
        $this->table('posts')->insert($data)->saveData();
    }
}
