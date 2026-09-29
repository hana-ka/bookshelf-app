<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IsbnSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_book_by_isbn(): void
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => 'Laravel入門',
                            'authors' => ['テスト作者'],
                            'description' => 'Laravelの入門書です。',
                            'imageLinks' => [
                                'thumbnail' => 'https://example.com/book.jpg',
                            ],
                            'publishedDate' => '2026-01-01',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->get('/books/isbn/9781234567890');

        $response->assertStatus(200);

        $response->assertJson([
            'title' => 'Laravel入門',
            'author' => 'テスト作者',
            'isbn' => '9781234567890',
            'description' => 'Laravelの入門書です。',
            'image_url' => 'https://example.com/book.jpg',
            'published_date' => '2026-01-01',
        ]);
    }

    public function test_returns_404_when_book_is_not_found(): void
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'totalItems' => 0,
            ], 200),
        ]);

        $response = $this->get('/books/isbn/9781234567890');

        $response->assertStatus(404);

        $response->assertJson([
            'error' => '書籍が見つかりませんでした。',
        ]);
    }

    public function test_returns_500_when_google_books_api_fails(): void
    {
        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([], 500),
        ]);

        $response = $this->get('/books/isbn/9781234567890');

        $response->assertStatus(500);

        $response->assertJson([
            'error' => '書籍情報の取得に失敗しました。',
        ]);
    }
}