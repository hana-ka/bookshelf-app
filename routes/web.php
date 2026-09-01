<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [BookController::class, 'index' ])
->name('books.index');

Route::get('/books/create', [BookController::class, 'create'])
    ->middleware('auth')
    ->name('books.create');

Route::get('/books/{book}', [BookController::class, 'show'])
    ->name('books.show');

Route::post('/books',
[BookController::class, 'store'])
    ->middleware('auth')
    ->name('books.store');

Route::post('/books/{book}/favorites',[FavoriteController::class, 'toggle'])
    ->middleware('auth')
    ->name('favorites.toggle');

// TODO: 仮ルート（レビュー投稿機能実装時にControllerへ変更）
Route::post('/books/{book}/reviews', function () {
    //
})->middleware('auth')->name('reviews.store');

// TODO: 仮ルート（レビューいいね機能実装時にControllerへ変更）
Route::post('/reviews/{review}/like', function () {
    //
})->middleware('auth')->name('reviews.like');

// TODO: 仮ルート（機能実装時にControllerへ変更）
Route::get('/ranking', function () {
    return 'ランキング';
})->name('ranking.index');

Route::get('/favorites', function () {
    return 'お気に入り';
})->name('favorites.index');

Route::get('/genres', function () {
    return 'ジャンル管理';
})->name('genres.index');