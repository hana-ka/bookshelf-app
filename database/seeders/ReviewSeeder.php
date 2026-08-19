<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = [
            ['user_id' => 1, 'book_id' => 1, 'rating' => 5, 'comment' => '猫の視点が面白く、何度でも読み返したくなりました。'],
            ['user_id' => 2, 'book_id' => 1, 'rating' => 4, 'comment' => '独特な世界観で楽しめました。'],
            ['user_id' => 3, 'book_id' => 1, 'rating' => 3, 'comment' => '少し難しかったですが興味深かったです。'],

            ['user_id' => 1, 'book_id' => 2, 'rating' => 5, 'comment' => '人間関係を見直すきっかけになりました。'],
            ['user_id' => 4, 'book_id' => 2, 'rating' => 4, 'comment' => '仕事にも活かせる内容でした。'],

            ['user_id' => 2, 'book_id' => 3, 'rating' => 5, 'comment' => 'プログラマー必読の一冊です。'],
            ['user_id' => 3, 'book_id' => 3, 'rating' => 4, 'comment' => 'コードを書く考え方が変わりました。'],
            ['user_id' => 5, 'book_id' => 3, 'rating' => 5, 'comment' => 'とても勉強になりました。'],

            ['user_id' => 1, 'book_id' => 4, 'rating' => 4, 'comment' => '人生に役立つ考え方が学べました。'],
            ['user_id' => 2, 'book_id' => 4, 'rating' => 5, 'comment' => '繰り返し読みたい内容です。'],
            ['user_id' => 5, 'book_id' => 4, 'rating' => 3, 'comment' => '少し難しかったです。'],

            ['user_id' => 3, 'book_id' => 5, 'rating' => 5, 'comment' => 'テンポよく読めました。'],
            ['user_id' => 4, 'book_id' => 5, 'rating' => 4, 'comment' => '主人公が魅力的でした。'],
            ['user_id' => 5, 'book_id' => 5, 'rating' => 3, 'comment' => '気軽に読める作品でした。'],

            ['user_id' => 1, 'book_id' => 6, 'rating' => 5, 'comment' => '人類史の見方が変わりました。'],
            ['user_id' => 2, 'book_id' => 6, 'rating' => 4, 'comment' => '内容が濃く勉強になりました。'],
            ['user_id' => 3, 'book_id' => 6, 'rating' => 3, 'comment' => '読むのに時間がかかりました。'],

            ['user_id' => 2, 'book_id' => 7, 'rating' => 5, 'comment' => '開発者なら読むべき一冊です。'],
            ['user_id' => 4, 'book_id' => 7, 'rating' => 5, 'comment' => '実践で役立つ内容でした。'],
            ['user_id' => 5, 'book_id' => 7, 'rating' => 4, 'comment' => '非常に参考になりました。'],

            ['user_id' => 1, 'book_id' => 8, 'rating' => 4, 'comment' => '考え方が前向きになりました。'],
            ['user_id' => 2, 'book_id' => 8, 'rating' => 4, 'comment' => '考え方の参考になりました。'],
            ['user_id' => 3, 'book_id' => 8, 'rating' => 5, 'comment' => '人生観が変わる本です。'],

            ['user_id' => 2, 'book_id' => 9, 'rating' => 4, 'comment' => '感情移入して読めました。'],
            ['user_id' => 4, 'book_id' => 9, 'rating' => 5, 'comment' => 'ラストが印象的でした。'],
            ['user_id' => 5, 'book_id' => 9, 'rating' => 3, 'comment' => '少し好みが分かれる作品です。'],

            ['user_id' => 1, 'book_id' => 10, 'rating' => 5, 'comment' => 'データの見方が変わりました。'],
            ['user_id' => 3, 'book_id' => 10, 'rating' => 4, 'comment' => '思い込みを見直せました。'],
            ['user_id' => 4, 'book_id' => 10, 'rating' => 4, 'comment' => '非常に読み応えがありました。'],

            ['user_id' => 2, 'book_id' => 11, 'rating' => 4, 'comment' => '物流の歴史がよく分かりました。'],
            ['user_id' => 3, 'book_id' => 11, 'rating' => 5, 'comment' => 'とても興味深い内容でした。'],
            ['user_id' => 5, 'book_id' => 11, 'rating' => 3, 'comment' => '勉強になりました。'],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}
