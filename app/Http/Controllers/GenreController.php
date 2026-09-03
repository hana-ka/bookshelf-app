<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::withCount('books')->get();

        return view('genres.index', compact('genres'));
    }

    public function create()
    {
        return view('genres.create');
    }

    // TODO: ジャンル登録機能
    public function store(Request $request)
    {
        //
    }

    public function show(Genre $genre)
    {
        $books = $genre->books()->paginate(10);

        return view('genres.show', compact('genre','books'));
    }

    // TODO: ジャンル編集画面
    public function edit(Genre $genre)
    {
        return view('genres.edit', compact('genre'));
    }

    // TODO: ジャンル編集機能
    public function update(Request $request)
    {
        //
    }

    // TODO: ジャンル削除
    public function destroy(Genre $genre)
    {
        //
    }
}
