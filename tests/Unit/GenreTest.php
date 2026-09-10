<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    public function test_genre_has_books_relation()
    {
        $genre = Genre::factory()->create();

        $books = Book::factory()->count(2)->create();

        $genre->books()->attach($books);

        $this->assertTrue($genre->books->contains($books[0]));
        $this->assertTrue($genre->books->contains($books[1]));
    }
}