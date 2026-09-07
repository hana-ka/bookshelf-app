<?php

namespace Tests\Unit;

use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_like_has_user_relation()
    {
        $user = User::factory()->create();

        $reviewLike = ReviewLike::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($reviewLike->user->is($user));
    }

    public function test_review_like_has_review_relation()
    {
        $review = Review::factory()->create();

        $reviewLike = ReviewLike::factory()->create([
            'review_id' => $review->id,
        ]);

        $this->assertTrue($reviewLike->review->is($review));
    }
}