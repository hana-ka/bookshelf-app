<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite_has_user_relation()
    {
        $user = User::factory()->create();

        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($favorite->user->is($user));
    }

    public function test_favorite_has_book_relation()
    {
        $book = Book::factory()->create();

        $favorite = Favorite::factory()->create([
            'book_id' => $book->id,
        ]);

        $this->assertTrue($favorite->book->is($book));
    }
}