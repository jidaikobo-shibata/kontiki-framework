<?php

use Phinx\Seed\AbstractSeed;

class InformationsSeeder extends AbstractSeed
{
    public function run() : void
    {
        // Fakerインスタンスを作成
        $faker = \Faker\Factory::create();

        $startDate = (new DateTime('-20 days'))->format('Y-m-d H:i:s');
        $endDate = (new DateTime('+20 days'))->format('Y-m-d H:i:s');

        // 割合設定
        $deletedAtRatio = 0.1; // 削除済みの割合
        $expiredAtRatio = 0.1; // 過去に期限切れの割合
        $futurePublishedRatio = 0.1; // 公開日が未来の日付の割合

        // 挿入するデータを準備
        $data = [];
        for ($i = 0; $i < 20; $i++) {
            // published_at の設定
            if ($faker->randomFloat(2, 0, 1) < $futurePublishedRatio) {
                // 未来の日付
                $publishedAt = $faker->dateTimeBetween('now', $endDate);
            } else {
                // 過去または現在の日付
                $publishedAt = $faker->dateTimeBetween($startDate, 'now');
            }

            // deleted_at の設定
            if ($faker->randomFloat(2, 0, 1) < $deletedAtRatio) {
                $deletedAt = $faker->dateTimeBetween('-20 days', 'now');
            } else {
                $deletedAt = null;
            }

            // expired_at の設定
            if ($faker->randomFloat(2, 0, 1) < $expiredAtRatio) {
                $expiredAt = $faker->dateTimeBetween('-20 days', 'now');
            } else {
                $expiredAt = $faker->optional(0.7, null)->dateTimeBetween('now', '+20 days');
            }

            $data[] = [
                'title' => $faker->sentence,
                'content' => $faker->paragraph,
                'slug' => $faker->slug,
                'is_draft' => $faker->boolean,
                'creator_id' => 1,
                'published_at' => $publishedAt->format('Y-m-d H:i:s'),
                'deleted_at' => $deletedAt ? $deletedAt->format('Y-m-d H:i:s') : null,
                'expired_at' => $expiredAt ? $expiredAt->format('Y-m-d H:i:s') : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        // テーブルにデータを挿入
        $this->table('informations')->insert($data)->saveData();
    }
}
