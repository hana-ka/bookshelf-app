<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $reviews = Review::all();

        foreach ($reviews as $review) {
            $likeCount = $likeCount = rand(0, 5);

            if ($likeCount === 0) {
                continue;
            }

            $likedUsers = $users->random($likeCount);

            $review->likedByUsers()->syncWithoutDetaching(
                $likedUsers->pluck('id')->toArray()
            );
        }
    }
}