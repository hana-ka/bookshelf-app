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

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_has_user_relation()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($book->user->is($user));
    }

    public function test_book_has_genres_relation()
    {
        $genres = Genre::factory()->count(2)->create();

        $book = Book::factory()->create();

        $book->genres()->attach($genres);

        $this->assertTrue($book->genres->contains($genres[0]));
        $this->assertTrue($book->genres->contains($genres[1]));
    }

    public function test_book_has_reviews_relation()
    {
        $book = Book::factory()->create();

        $reviews = Review::factory()
            ->count(2)
            ->create([
                'book_id' => $book->id,
            ]);

        $this->assertTrue($book->reviews->contains($reviews[0]));
        $this->assertTrue($book->reviews->contains($reviews[1]));
    }

    public function test_book_has_favorites_relation()
    {
        $book = Book::factory()->create();

        $favorites = Favorite::factory()
            ->count(2)
            ->create([
                'book_id' => $book->id,
            ]);

        $this->assertTrue($book->favorites->contains($favorites[0]));
        $this->assertTrue($book->favorites->contains($favorites[1]));
    }

    public function test_book_has_favorited_users_relation()
    {
        $book = Book::factory()->create();

        $users = User::factory()->count(2)->create();

        $book->favoritedUsers()->attach($users);

        $this->assertTrue($book->favoritedUsers->contains($users[0]));
        $this->assertTrue($book->favoritedUsers->contains($users[1]));
    }
}
