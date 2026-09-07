<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_books_relation()
    {
        $user = User::factory()->create();

        $books = Book::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $this->assertTrue($user->books->contains($books[0]));
        $this->assertTrue($user->books->contains($books[1]));
    }

    public function test_user_has_reviews_relation()
    {
        $user = User::factory()->create();

        $reviews = Review::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $this->assertTrue($user->reviews->contains($reviews[0]));
        $this->assertTrue($user->reviews->contains($reviews[1]));
    }

    public function test_user_has_favorites_relation()
    {
        $user = User::factory()->create();

        $favorites = Favorite::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $this->assertTrue($user->favorites->contains($favorites[0]));
        $this->assertTrue($user->favorites->contains($favorites[1]));
    }

    public function test_user_has_review_likes_relation()
    {
        $user = User::factory()->create();

        $reviewLikes = ReviewLike::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $this->assertTrue($user->reviewLikes->contains($reviewLikes[0]));
        $this->assertTrue($user->reviewLikes->contains($reviewLikes[1]));
    }

    public function test_user_has_favorite_books_relation()
    {
        $user = User::factory()->create();

        $books = Book::factory()->count(2)->create();

        $user->favoriteBooks()->attach($books);

        $this->assertTrue($user->favoriteBooks->contains($books[0]));
        $this->assertTrue($user->favoriteBooks->contains($books[1]));
    }

    public function test_user_has_liked_reviews_relation()
    {
        $user = User::factory()->create();

        $reviews = Review::factory()->count(2)->create();

        $user->likedReviews()->attach($reviews);

        $this->assertTrue($user->likedReviews->contains($reviews[0]));
        $this->assertTrue($user->likedReviews->contains($reviews[1]));
    }
}