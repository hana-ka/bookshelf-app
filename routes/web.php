<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewLikeController;

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

Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])
    ->middleware('auth')
    ->name('reviews.store');

Route::post('/reviews/{review}/like', [ReviewLikeController::class, 'toggle'])
    ->middleware('auth')
    ->name('reviews.like');

Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])
    ->middleware('auth')
    ->name('reviews.edit');

Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
    ->middleware('auth')
    ->name('reviews.destroy');

Route::get('/books/{book}/edit', [BookController::class, 'edit'])
    ->middleware('auth')
    ->name('books.edit');

Route::delete('/books/{book}', [BookController::class, 'destroy'])
    ->middleware('auth')
    ->name('books.destroy');

Route::post('/books', [BookController::class, 'store'])
    ->name('books.store');

Route::put('/reviews/{review}', [ReviewController::class, 'update'])
    ->middleware('auth')
    ->name('reviews.update');

Route::put('/books/{book}', [BookController::class, 'update'])
    ->middleware('auth')
    ->name('books.update');

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