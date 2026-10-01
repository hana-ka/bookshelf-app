<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Toggle the authenticated user's favorite status for the specified book.
     */
    public function toggle(Book $book): RedirectResponse
    {
        $user = Auth::user();

        if ($user->favoriteBooks()->where('books.id', $book->id)->exists()) {
            $user->favoriteBooks()->detach($book->id);
        } else {
            $user->favoriteBooks()->attach($book->id);
        }

        return redirect()->route('books.show', $book);
    }

    /**
     * Display the authenticated user's favorite books.
     */
    public function index(): View
    {
        $user = Auth::user();

        $books = $user->favoriteBooks()->paginate(10);

        return view('favorites.index', compact('books'));
    }
}
