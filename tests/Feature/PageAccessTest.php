<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_is_displayed()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_register_page_is_displayed()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_book_index_page_is_displayed()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_book_show_page_is_displayed()
    {
        $book = Book::factory()->create();

        $response = $this->get("/books/{$book->id}");

        $response->assertStatus(200);
    }

    public function test_book_create_page_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/books/create');

        $response->assertStatus(200);
    }

    public function test_book_edit_page_is_displayed()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get("/books/{$book->id}/edit");

        $response->assertStatus(200);
    }

    public function test_review_edit_page_is_displayed()
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get("/reviews/{$review->id}/edit");

        $response->assertStatus(200);
    }

    public function test_favorites_page_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/favorites');

        $response->assertStatus(200);
    }

    public function test_genre_index_page_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/genres');

        $response->assertStatus(200);
    }

    public function test_genre_show_page_is_displayed()
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)
            ->get("/genres/{$genre->id}");

        $response->assertStatus(200);
    }

    public function test_genre_create_page_is_displayed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/genres/create');

        $response->assertStatus(200);
    }

    public function test_genre_edit_page_is_displayed()
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)
            ->get("/genres/{$genre->id}/edit");

        $response->assertStatus(200);
    }

    public function test_ranking_page_is_displayed()
    {
        $response = $this->get('/ranking');

        $response->assertStatus(200);
    }
}