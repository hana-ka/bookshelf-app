<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ReviewRequest;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;


class ReviewController extends Controller
{
    public function store (ReviewRequest $request, Book $book)
    {
        $book->reviews()->create([
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->route('books.show', $book);
    }
}
