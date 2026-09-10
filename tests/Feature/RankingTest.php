<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_ranking_page_is_displayed()
    {
        $response = $this->get('/ranking');

        $response->assertStatus(200);
    }

    public function test_ranking_displays_books_in_rating_order()
    {
        $user = User::factory()->create();

        $lowRatedBook = Book::factory()->create([
            'title' => '低評価の本',
        ]);

        $highRatedBook = Book::factory()->create([
            'title' => '高評価の本',
        ]);

        Review::factory()->create([
            'book_id' => $lowRatedBook->id,
            'user_id' => $user->id,
            'rating' => 2,
        ]);

        Review::factory()->create([
            'book_id' => $highRatedBook->id,
            'user_id' => $user->id,
            'rating' => 5,
        ]);

        $response = $this->get('/ranking');

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            '高評価の本',
            '低評価の本',
        ]);
    }

    public function test_books_without_reviews_are_not_in_ranking()
    {
        $reviewedBook = Book::factory()->create([
            'title' => 'レビューありの本',
        ]);

        $unreviewedBook = Book::factory()->create([
            'title' => 'レビューなしの本',
        ]);

        $user = User::factory()->create();

        Review::factory()->create([
            'book_id' => $reviewedBook->id,
            'user_id' => $user->id,
            'rating' => 5,
        ]);

        $response = $this->get('/ranking');

        $response->assertStatus(200);
        $response->assertSee('レビューありの本');
        $response->assertDontSee('レビューなしの本');
    }

    public function test_ranking_displays_top_10_books()
    {
        $user = User::factory()->create();

        for ($i = 1; $i <= 10; $i++) {
            $book = Book::factory()->create([
                'title' => "ランキング{$i}位",
            ]);

            Review::factory()->create([
                'book_id' => $book->id,
                'user_id' => $user->id,
                'rating' => 5,
            ]);
        }

        $book = Book::factory()->create([
            'title' => 'ランキング対象外',
        ]);

        Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 1,
        ]);

        $response = $this->get('/ranking');

        $response->assertStatus(200);

        for ($i = 1; $i <= 10; $i++) {
            $response->assertSee("ランキング{$i}位");
        }

        $response->assertDontSee('ランキング対象外');
    }
}