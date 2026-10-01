<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_books_by_keyword(): void
    {
        $targetBook = Book::factory()->create([
            'title' => 'Laravel入門',
        ]);

        $otherBook = Book::factory()->create([
            'title' => 'PHP入門',
        ]);

        $response = $this->get('/?keyword=Laravel');

        $response->assertStatus(200);
        $response->assertSee($targetBook->title);
        $response->assertDontSee($otherBook->title);
    }

    public function test_can_search_books_by_author(): void
    {
        $targetBook = Book::factory()->create([
            'title' => 'テスト書籍',
            'author' => 'テスト作者',
        ]);

        $otherBook = Book::factory()->create([
            'title' => '別の書籍',
            'author' => '別の作者',
        ]);

        $response = $this->get('/?keyword=テスト作者');

        $response->assertStatus(200);
        $response->assertSee($targetBook->title);
        $response->assertDontSee($otherBook->title);
    }

    public function test_can_filter_books_by_genre(): void
    {
        $genre = Genre::factory()->create([
            'name' => 'プログラミング',
        ]);

        $targetBook = Book::factory()->create([
            'title' => 'Laravel入門',
        ]);

        $otherBook = Book::factory()->create([
            'title' => '料理入門',
        ]);

        $targetBook->genres()->attach($genre);

        $response = $this->get('/?genre='.$genre->id);

        $response->assertStatus(200);
        $response->assertSee($targetBook->title);
        $response->assertDontSee($otherBook->title);
    }
}
