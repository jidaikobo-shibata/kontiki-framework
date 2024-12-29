<?php

use Phinx\Seed\AbstractSeed;

class PostsSeeder extends AbstractSeed
{
    public function run() : void
    {
        // Fakerインスタンスを作成
        $faker = \Faker\Factory::create();

        $startDate = (new DateTime('-20 days'))->format('Y-m-d H:i:s');
        $endDate = (new DateTime('+20 days'))->format('Y-m-d H:i:s');

        // 挿入するデータを準備
        $data = [];
        for ($i = 0; $i < 20; $i++) {
            $publishedAt = $faker->optional(0.8)->dateTimeBetween($startDate, $endDate);
            $deletedAt = $faker->optional(0.7, null)->dateTimeBetween('-20 days', '-20 days');

            $data[] = [
                'title' => $faker->sentence,
                'content' => $faker->paragraph,
                'slug' => $faker->slug,
                'is_draft' => $faker->boolean,
                'creator_id' => 1,
                'published_at' => $publishedAt ? $publishedAt->format('Y-m-d H:i:s') : null,
                'deleted_at' => $deletedAt ? $deletedAt->format('Y-m-d H:i:s') : null,
                'expired_at' => $deletedAt ? $deletedAt->format('Y-m-d H:i:s') : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        // テーブルにデータを挿入
        $this->table('posts')->insert($data)->saveData();
    }
}
