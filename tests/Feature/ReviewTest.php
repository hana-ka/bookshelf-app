<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_can_be_created()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $data = [
            'rating' => 5,
            'comment' => 'とても面白い本でした。',
        ];

        $response = $this->actingAs($user)
            ->post("/books/{$book->id}/reviews", $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'とても面白い本でした。',
        ]);
    }

    public function test_review_creation_redirects_to_show_page()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $data = [
            'rating' => 5,
            'comment' => 'とても面白い本でした。',
        ];

        $response = $this->actingAs($user)
            ->post("/books/{$book->id}/reviews", $data);

        $response->assertRedirect(route('books.show', $book));
    }

    public function test_review_creation_displays_success_message()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $data = [
            'rating' => 5,
            'comment' => 'とても面白い本でした。',
        ];

        $response = $this->actingAs($user)
            ->post("/books/{$book->id}/reviews", $data);

        $response->assertSessionHas('success', 'レビューを投稿しました。');
    }

    public function test_review_creation_validation_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $data = [
            'rating' => 6,
            'comment' => [],
        ];

        $response = $this->actingAs($user)
            ->post("/books/{$book->id}/reviews", $data);

        $response->assertSessionHasErrors([
            'rating' => '評価は１〜５の範囲で選択してください。',
            'comment' => 'コメントは文字列で入力してください。',
        ]);
    }

    public function test_guest_cannot_create_review()
    {
        $book = Book::factory()->create();

        $response = $this->post("/books/{$book->id}/reviews", [
            'rating' => 5,
            'comment' => 'ゲストからのレビュー',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('reviews', [
            'book_id' => $book->id,
            'comment' => 'ゲストからのレビュー',
        ]);
    }

    public function test_review_can_be_updated()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 3,
            'comment' => '普通でした。',
        ]);

        $data = [
            'rating' => 5,
            'comment' => 'とても面白かったです。',
        ];

        $response = $this->actingAs($user)
            ->put("/reviews/{$review->id}", $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 5,
            'comment' => 'とても面白かったです。',
        ]);
    }

    public function test_review_update_validation_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $data = [
            'rating' => 0,
            'comment' => [],
        ];

        $response = $this->actingAs($user)
            ->put("/reviews/{$review->id}", $data);

        $response->assertSessionHasErrors([
            'rating' => '評価は１〜５の範囲で選択してください。',
            'comment' => 'コメントは文字列で入力してください。',
        ]);
    }

    public function test_review_update_redirects_to_show_page()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $data = [
            'rating' => 5,
            'comment' => '更新しました。',
        ];

        $response = $this->actingAs($user)
            ->put("/reviews/{$review->id}", $data);

        $response->assertRedirect(route('books.show', $book));
    }

    public function test_review_update_displays_success_message()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $data = [
            'rating' => 5,
            'comment' => '更新しました。',
        ];

        $response = $this->actingAs($user)
            ->put("/reviews/{$review->id}", $data);

        $response->assertSessionHas('success', 'レビューを更新しました。');
    }

    public function test_guest_cannot_update_review()
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $response = $this->put("/reviews/{$review->id}", [
            'rating' => 5,
            'comment' => 'ゲストからの更新',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => $review->rating,
            'comment' => $review->comment,
        ]);
    }

    public function test_review_can_be_deleted()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
            ->delete("/reviews/{$review->id}");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_review_deletion_redirects_to_show_page()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
            ->delete("/reviews/{$review->id}");

        $response->assertRedirect(route('books.show', $book));
    }

    public function test_review_deletion_displays_success_message()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
            ->delete("/reviews/{$review->id}");

        $response->assertSessionHas('success', 'レビューを削除しました。');
    }

    public function test_user_cannot_update_another_users_review()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($otherUser)
            ->put("/reviews/{$review->id}", [
                'rating' => 5,
                'comment' => '不正な更新',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_review()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($otherUser)
            ->delete("/reviews/{$review->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_guest_cannot_delete_review()
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $response = $this->delete("/reviews/{$review->id}");

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
        ]);
    }
}