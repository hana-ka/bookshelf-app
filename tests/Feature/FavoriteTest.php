<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_can_be_added_to_favorites()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)
            ->post("/books/{$book->id}/favorites");

        $response->assertStatus(302);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_favorite_creation_redirects_to_show_page()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)
            ->post("/books/{$book->id}/favorites");

        $response->assertRedirect(route('books.show', $book));
    }

    public function test_book_can_be_removed_from_favorites()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Favorite::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
            ->post("/books/{$book->id}/favorites");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_favorite_deletion_redirects_to_show_page()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Favorite::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
            ->post("/books/{$book->id}/favorites");

        $response->assertRedirect(route('books.show', $book));
    }

    public function test_book_can_be_re_added_to_favorites()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Favorite::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($user)
            ->post("/books/{$book->id}/favorites");

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($user)
            ->post("/books/{$book->id}/favorites");

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_guest_cannot_add_book_to_favorites()
    {
        $book = Book::factory()->create();

        $response = $this->post("/books/{$book->id}/favorites");

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('favorites', [
            'book_id' => $book->id,
        ]);
    }

    public function test_favorites_page_displays_user_favorites()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Favorite::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
            ->get('/favorites');

        $response->assertStatus(200);
        $response->assertSee($book->title);
    }

    public function test_guest_cannot_view_favorites_page()
    {
        $response = $this->get('/favorites');

        $response->assertRedirect('/login');
    }
}