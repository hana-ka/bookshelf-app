<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_sort_books_by_latest(): void
    {
        $oldBook = Book::factory()->create([
            'title' => '古い本',
            'created_at' => '2026-01-01 00:00:00',
        ]);

        $newBook = Book::factory()->create([
            'title' => '新しい本',
            'created_at' => '2026-02-01 00:00:00',
        ]);

        $response = $this->get('/?sort=newest');

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $newBook->title,
            $oldBook->title,
        ]);
    }

    public function test_can_sort_books_by_oldest(): void
    {
        $oldBook = Book::factory()->create([
            'title' => '古い本',
            'created_at' => '2026-01-01 00:00:00',
        ]);

        $newBook = Book::factory()->create([
            'title' => '新しい本',
            'created_at' => '2026-02-01 00:00:00',
        ]);

        $response = $this->get('/?sort=oldest');

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $oldBook->title,
            $newBook->title,
        ]);
    }

    public function test_can_sort_books_by_rating(): void
    {
        $highRatingBook = Book::factory()->create([
            'title' => '高評価の本',
        ]);

        $lowRatingBook = Book::factory()->create([
            'title' => '低評価の本',
        ]);

        $user = User::factory()->create();

        Review::factory()->create([
            'book_id' => $highRatingBook->id,
            'user_id' => $user->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'book_id' => $lowRatingBook->id,
            'user_id' => $user->id,
            'rating' => 2,
        ]);

        $response = $this->get('/?sort=rating');

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $highRatingBook->title,
            $lowRatingBook->title,
        ]);
    }

    public function test_can_sort_books_by_title(): void
    {
        $bookA = Book::factory()->create([
            'title' => 'Aの本',
        ]);

        $bookB = Book::factory()->create([
            'title' => 'Bの本',
        ]);

        $response = $this->get('/?sort=title');

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $bookA->title,
            $bookB->title,
        ]);
    }
}