<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_has_user_relation()
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($review->user->is($user));
    }

    public function test_review_has_book_relation()
    {
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $this->assertTrue($review->book->is($book));
    }

    public function test_review_has_review_likes_relation()
    {
        $review = Review::factory()->create();

        $reviewLikes = ReviewLike::factory()
            ->count(2)
            ->create([
                'review_id' => $review->id,
            ]);

        $this->assertTrue($review->reviewLikes->contains($reviewLikes[0]));
        $this->assertTrue($review->reviewLikes->contains($reviewLikes[1]));
    }

    public function test_review_has_liked_by_users_relation()
    {
        $review = Review::factory()->create();

        $users = User::factory()->count(2)->create();

        $review->likedByUsers()->attach($users);

        $this->assertTrue($review->likedByUsers->contains($users[0]));
        $this->assertTrue($review->likedByUsers->contains($users[1]));
    }
}