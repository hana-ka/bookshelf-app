<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Resources\BookResource;
use App\Http\Resources\BookDetailResource;
use App\Http\Requests\Api\BookRequest;
use Illuminate\Support\Facades\Auth;


class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('genres')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if($request->filled('keyword')){
            $query->where(function ($q) use ($request){
                $q->where('title', 'like', '%'. $request->keyword .'%')->orWhere('author', 'like', '%' . $request->keyword .'%');
            });
        }

        if($request->filled('genre')){
            $query->whereHas('genres', function ($q) use ($request){
                $q->where('genres.id', $request->genre);
            });
        }

        $books = $query->paginate(10);

        return BookResource::collection($books);
    }

    public function show(Book $book)
    {
        $book->load([
            'genres',
            'reviews.user',
        ]);

        return new BookDetailResource($book);
    }

    public function store(BookRequest $request)
    {
        $user = Auth::user();

        $book = $user->books()->create([
            'title' =>$request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'published_date' => $request->published_date,
            'description' => $request->description,
            'image_url' => $request->image_url,
        ]);

        $book->genres()->sync($request->genres);

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    public function update(BookRequest $request, Book $book)
    {
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'published_date' => $request->published_date,
            'description' => $request->description,
            'image_url' => $request->image_url,
        ]);

        $book->genres()->sync($request->genres);

        return new BookResource($book);

    }
}
