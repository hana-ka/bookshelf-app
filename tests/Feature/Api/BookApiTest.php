<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_list_can_be_retrieved()
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        $book->genres()->attach($genre);

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'isbn',
                        'published_date',
                        'description',
                        'image_url',
                        'average_rating',
                        'review_count',
                        'genres',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonFragment([
                'id' => $book->id,
                'title' => $book->title,
            ]);
    }

    public function test_books_can_be_searched_by_keyword()
    {
        $matchedBook = Book::factory()->create([
            'title' => 'Laravel入門',
            'author' => '田中太郎',
        ]);

        $otherBook = Book::factory()->create([
            'title' => 'PHP入門',
            'author' => '山田花子',
        ]);

        $response = $this->getJson('/api/v1/books?keyword=Laravel');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $matchedBook->id,
                'title' => 'Laravel入門',
            ])
            ->assertJsonMissing([
                'id' => $otherBook->id,
                'title' => 'PHP入門',
            ]);
    }

    public function test_books_can_be_filtered_by_genre()
    {
        $targetGenre = Genre::factory()->create();
        $otherGenre = Genre::factory()->create();

        $targetBook = Book::factory()->create([
            'title' => '対象書籍',
        ]);

        $otherBook = Book::factory()->create([
            'title' => '別ジャンル書籍',
        ]);

        $targetBook->genres()->attach($targetGenre);
        $otherBook->genres()->attach($otherGenre);

        $response = $this->getJson(
            "/api/v1/books?genre={$targetGenre->id}"
        );

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $targetBook->id,
                'title' => '対象書籍',
            ])
            ->assertJsonMissing([
                'id' => $otherBook->id,
                'title' => '別ジャンル書籍',
            ]);
    }

    public function test_book_list_is_paginated_by_20()
    {
        Book::factory()->count(21)->create();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonPath('meta.per_page', 20)
            ->assertJsonCount(20, 'data');
    }

    public function test_book_list_includes_average_rating_and_review_count()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $book = Book::factory()->create();

        Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user1->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user2->id,
            'rating' => 3,
        ]);

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $book->id,
                'average_rating' => '4.0000',
                'review_count' => 2,
            ]);
    }

    public function test_book_detail_can_be_retrieved()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create();

        $book->genres()->attach($genre);

        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'とても面白かったです。',
        ]);

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'author',
                    'isbn',
                    'published_date',
                    'description',
                    'image_url',
                    'genres',
                    'reviews',
                ],
            ])
            ->assertJsonFragment([
                'id' => $genre->id,
                'name' => $genre->name,
            ])
            ->assertJsonFragment([
                'id' => $review->id,
                'rating' => 5,
                'comment' => 'とても面白かったです。',
            ]);
    }

    public function test_nonexistent_book_returns_404()
    {
        $response = $this->getJson('/api/v1/books/99999');

        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_create_book()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2026-01-01',
            'description' => 'テスト説明',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)
            ->postJson('/api/v1/books', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'テスト書籍',
                'author' => 'テスト著者',
            ]);

        $this->assertDatabaseHas('books', [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'user_id' => $user->id,
        ]);

        $book = Book::where('isbn', '1234567890123')->first();

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    public function test_guest_cannot_create_book()
    {
        $genre = Genre::factory()->create();

        $response = $this->postJson('/api/v1/books', [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2026-01-01',
            'genres' => [$genre->id],
        ]);

        $response->assertStatus(401);
    }

    public function test_book_creation_validation_fails_with_invalid_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/v1/books', [
                'title' => '',
                'author' => '',
                'isbn' => '123',
                'published_date' => 'invalid-date',
                'description' => [],
                'image_url' => 'invalid-url',
                'genres' => [],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'author',
                'isbn',
                'published_date',
                'description',
                'image_url',
                'genres',
            ]);
    }

    public function test_book_creation_fails_with_duplicate_isbn()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        Book::factory()->create([
            'isbn' => '1234567890123',
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/v1/books', [
                'title' => 'テスト書籍',
                'author' => 'テスト著者',
                'isbn' => '1234567890123',
                'published_date' => '2026-01-01',
                'genres' => [$genre->id],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('isbn');
    }

    public function test_authenticated_user_can_update_book()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
            'title' => '更新前',
        ]);

        $response = $this->actingAs($user)
            ->putJson("/api/v1/books/{$book->id}", [
                'title' => '更新後',
                'author' => $book->author,
                'isbn' => $book->isbn,
                'published_date' => $book->published_date,
                'description' => $book->description,
                'image_url' => $book->image_url,
                'genres' => [$genre->id],
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => '更新後',
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後',
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    public function test_book_update_validation_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->putJson("/api/v1/books/{$book->id}", [
                'title' => '',
                'author' => '',
                'isbn' => '123',
                'published_date' => 'invalid-date',
                'description' => [],
                'image_url' => 'invalid-url',
                'genres' => [],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'author',
                'isbn',
                'published_date',
                'description',
                'image_url',
                'genres',
            ]);
    }

    public function test_book_can_be_updated_with_its_own_isbn()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->putJson("/api/v1/books/{$book->id}", [
                'title' => '更新後の書籍',
                'author' => $book->author,
                'isbn' => $book->isbn,
                'published_date' => $book->published_date,
                'description' => $book->description,
                'image_url' => $book->image_url,
                'genres' => [$genre->id],
            ]);

        $response->assertStatus(200);
    }

    public function test_nonexistent_book_cannot_be_updated()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)
            ->putJson('/api/v1/books/99999', [
                'title' => '更新後の書籍',
                'author' => 'テスト著者',
                'isbn' => '1234567890123',
                'published_date' => '2026-01-01',
                'genres' => [$genre->id],
            ]);

        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_delete_book()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    public function test_book_deletion_handles_related_data()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $genre = Genre::factory()->create();

        $book->genres()->attach($genre);

        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $favoriteUser = User::factory()->create();

        Favorite::factory()->create([
            'user_id' => $favoriteUser->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($user)
            ->deleteJson("/api/v1/books/{$book->id}");

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);

        $this->assertDatabaseMissing('favorites', [
            'book_id' => $book->id,
        ]);

        $this->assertDatabaseMissing('book_genre', [
            'book_id' => $book->id,
        ]);
    }

    public function test_guest_cannot_delete_book()
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(401);
    }

    public function test_nonexistent_book_cannot_be_deleted()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->deleteJson('/api/v1/books/99999');

        $response->assertStatus(404);
    }

    public function test_user_cannot_update_another_users_book()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherUser)
            ->putJson("/api/v1/books/{$book->id}", [
                'title' => '不正な更新',
                'author' => $book->author,
                'isbn' => $book->isbn,
                'published_date' => $book->published_date,
                'description' => $book->description,
                'image_url' => $book->image_url,
                'genres' => [$genre->id],
            ]);

        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_another_users_book()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherUser)
            ->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(403);
    }
}