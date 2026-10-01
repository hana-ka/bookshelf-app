<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyReadingReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_display_my_reading_report(): void
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create([
            'name' => 'プログラミング',
        ]);

        $book1 = Book::factory()->create([
            'title' => 'Laravel入門',
            'author' => 'テスト作者',
        ]);

        $book2 = Book::factory()->create([
            'title' => 'PHP入門',
            'author' => 'テスト作者2',
        ]);

        $book1->genres()->attach($genre);
        $book2->genres()->attach($genre);

        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book1->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book2->id,
            'rating' => 3,
        ]);

        $response = $this->actingAs($user)
            ->get('/reports');

        $response->assertStatus(200);

        $response->assertViewHas('stats', function ($stats) {
            return $stats['summary']['total_reviews'] === 2
                && $stats['summary']['books_read'] === 2
                && $stats['summary']['average_rating'] === '4.0000';
        });

        $response->assertViewHas('stats', function ($stats) {
            return $stats['rating_distribution'][0] === 0
                && $stats['rating_distribution'][1] === 0
                && $stats['rating_distribution'][2] === 1
                && $stats['rating_distribution'][3] === 0
                && $stats['rating_distribution'][4] === 1;
        });

        $response->assertViewHas('stats', function ($stats) {
            return $stats['top_rated_books']->count() === 1
                && $stats['top_rated_books']->first()['title'] === 'Laravel入門'
                && $stats['top_rated_books']->first()['author'] === 'テスト作者'
                && $stats['top_rated_books']->first()['rating'] === 5;
        });

        $response->assertViewHas('stats', function ($stats) {
            $genreRating = $stats['genre_ratings']->first();

            return $genreRating['name'] === 'プログラミング'
                && $genreRating['count'] === 2
                && $genreRating['average_rating'] === 4;
        });

        $response->assertSee('Laravel入門');
        $response->assertSee('プログラミング');
    }

    public function test_guest_cannot_access_my_reading_report(): void
    {
        $response = $this->get('/reports');

        $response->assertRedirect('/login');
    }
}
