<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviewLikes = [
            1 => [2, 3],
            2 => [1],
            3 => [],
            4 => [2, 5],
            5 => [1],
            6 => [1, 4],
            7 => [2],
            8 => [3, 4],
            9 => [5],
            10 => [3],
            11 => [],
            12 => [1, 2],
            13 => [5],
            14 => [2, 4],
            15 => [],
            16 => [4],
            17 => [1, 5],
            18 => [3],
            19 => [2],
            20 => [5],
            21 => [2, 4],
            22 => [1],
            23 => [3, 5],
            24 => [],
            25 => [4],
            26 => [2],
            27 => [1, 5],
            28 => [4],
            29 => [],
            30 => [1, 3],
            31 => [2],
            32 => [4],
        ];

        $reviews = Review::all();

        foreach ($reviews as $review) {
            $review->likedUsers()->syncWithoutDetaching(
                $reviewLikes[$review->id]
            );
        }
    }
}
