<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_can_be_created()
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
            ->post('/books', $data);

        $response->assertStatus(302);

        $book = Book::where('isbn', '1234567890123')->first();

        $this->assertNotNull($book);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    public function test_book_creation_redirects_to_show_page()
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
            ->post('/books', $data);

        $book = Book::where('isbn', '1234567890123')->first();

        $response->assertRedirect(route('books.show', $book));
    }

    public function test_book_creation_displays_success_message()
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
            ->post('/books', $data);

        $response->assertSessionHas('success', '書籍を登録しました。');
    }

    public function test_book_creation_validation_fails_with_invalid_data()
    {
        $user = User::factory()->create();

        $data = [
            'title' => '',
            'author' => '',
            'isbn' => '123',
            'published_date' => 'invalid-date',
            'description' => [],
            'image_url' => 'invalid-url',
            'genres' => [],
        ];

        $response = $this->actingAs($user)
            ->post('/books', $data);

        $response->assertSessionHasErrors([
            'title' => 'タイトルを入力してください。',
            'author' => '著者名を入力してください。',
            'isbn' => 'ISBNは13桁で入力してください。',
            'published_date' => '正しい日付を入力してください。',
            'description' => '説明は文字列で入力してください。',
            'image_url' => '画像URLは正しいURL形式で入力してください。',
            'genres' => 'ジャンルを1つ以上選択してください。',
        ]);
    }

    public function test_book_creation_fails_with_duplicate_isbn()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        Book::factory()->create([
            'isbn' => '1234567890123',
        ]);

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
            ->post('/books', $data);

        $response->assertSessionHasErrors([
            'isbn' => 'このISBNは既に登録されています。',
        ]);
    }

    public function test_guest_cannot_view_book_create_page()
    {
        $response = $this->get('/books/create');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_create_book()
    {
        $response = $this->post('/books', [
            'title' => 'ゲスト書籍',
            'author' => 'ゲスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2026-01-01',
            'description' => 'ゲストからの登録',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [],
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('books', [
            'title' => 'ゲスト書籍',
        ]);
    }

    public function test_book_can_be_updated()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $newGenre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $book->genres()->attach($genre);

        $data = [
            'title' => '更新後の書籍',
            'author' => '更新後の著者',
            'isbn' => $book->isbn,
            'published_date' => '2026-02-01',
            'description' => '更新後の説明',
            'image_url' => 'https://example.com/new-image.jpg',
            'genres' => [$newGenre->id],
        ];

        $response = $this->actingAs($user)
            ->put("/books/{$book->id}", $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後の書籍',
            'author' => '更新後の著者',
            'isbn' => $book->isbn,
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $newGenre->id,
        ]);

        $this->assertDatabaseMissing('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    public function test_book_can_be_updated_with_its_own_isbn()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'title' => '更新後の書籍',
            'author' => '更新後の著者',
            'isbn' => $book->isbn,
            'published_date' => '2026-02-01',
            'description' => '更新後の説明',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)
            ->put("/books/{$book->id}", $data);

        $response->assertSessionDoesntHaveErrors();
    }

    public function test_book_update_validation_fails_with_invalid_data()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'title' => '',
            'author' => '',
            'isbn' => '123',
            'published_date' => 'invalid-date',
            'description' => [],
            'image_url' => 'invalid-url',
            'genres' => [],
        ];

        $response = $this->actingAs($user)
            ->put("/books/{$book->id}", $data);

        $response->assertSessionHasErrors([
            'title' => 'タイトルを入力してください。',
            'author' => '著者名を入力してください。',
            'isbn' => 'ISBNは13桁で入力してください。',
            'published_date' => '正しい日付を入力してください。',
            'description' => '説明は文字列で入力してください。',
            'image_url' => '画像URLは正しいURL形式で入力してください。',
            'genres' => 'ジャンルを1つ以上選択してください。',
        ]);
    }

    public function test_book_update_redirects_to_show_page()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'title' => '更新後の書籍',
            'author' => '更新後の著者',
            'isbn' => $book->isbn,
            'published_date' => '2026-02-01',
            'description' => '更新後の説明',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)
            ->put("/books/{$book->id}", $data);

        $response->assertRedirect(route('books.show', $book));
    }

    public function test_book_update_displays_success_message()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'title' => '更新後の書籍',
            'author' => '更新後の著者',
            'isbn' => $book->isbn,
            'published_date' => '2026-02-01',
            'description' => '更新後の説明',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [$genre->id],
        ];

        $response = $this->actingAs($user)
            ->put("/books/{$book->id}", $data);

        $response->assertSessionHas('success', '書籍情報を更新しました。');
    }

    public function test_guest_cannot_view_book_edit_page()
    {
        $book = Book::factory()->create();

        $response = $this->get("/books/{$book->id}/edit");

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_update_book()
    {
        $book = Book::factory()->create([
            'title' => '更新前書籍',
        ]);

        $response = $this->put("/books/{$book->id}", [
            'title' => 'ゲスト更新書籍',
            'author' => $book->author,
            'isbn' => $book->isbn,
            'published_date' => $book->published_date,
            'description' => $book->description,
            'image_url' => $book->image_url,
            'genres' => [],
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新前書籍',
        ]);
    }

    public function test_user_cannot_update_another_users_book()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $owner->id,
            'title' => '元のタイトル',
        ]);

        $response = $this->actingAs($otherUser)
            ->put("/books/{$book->id}", [
                'title' => '不正な更新',
                'author' => $book->author,
                'isbn' => $book->isbn,
                'published_date' => $book->published_date,
                'description' => $book->description,
                'image_url' => $book->image_url,
                'genres' => [$genre->id],
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '元のタイトル',
        ]);
    }

    public function test_book_can_be_deleted()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->delete("/books/{$book->id}");

        $response->assertStatus(302);

        $response->assertRedirect(route('books.index'));

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

        Favorite::factory()->create([
            'book_id' => $book->id,
        ]);

        $this->actingAs($user)
            ->delete("/books/{$book->id}");

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

    public function test_book_deletion_displays_success_message()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->delete("/books/{$book->id}");

        $response->assertSessionHas('success', '書籍を削除しました。');
    }

    public function test_guest_cannot_delete_book()
    {
        $book = Book::factory()->create();

        $response = $this->delete("/books/{$book->id}");

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_book()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherUser)
            ->delete("/books/{$book->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
        ]);
    }
}