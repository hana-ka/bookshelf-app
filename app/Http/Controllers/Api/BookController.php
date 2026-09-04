<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Resources\BookResource;

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
}
