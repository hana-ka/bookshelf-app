<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Book $book)
    {
        $user = Auth::user();

        if($user->favoriteBooks->contains($book->id)){
            $user->favoriteBooks()->detach($book->id);
        }else{
                $user->favoriteBooks()->attach($book->id);
        }

        return redirect()->route('books.show', $book);
    }

    public function index()
    {
        $user = Auth::user();

        $books = $user->favoriteBooks()->paginate(10);

        return view('favorites.index', compact('books'));
    }
}
