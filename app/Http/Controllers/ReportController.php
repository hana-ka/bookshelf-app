<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $summary = [
            'total_reviews' => $user->reviews()->count(),
            'books_read' => $user->reviews()
                ->distinct('book_id')
                ->count('book_id'),
            'average_rating' => $user->reviews()->avg('rating') ?? 0,
        ];

        $ratingDistribution = collect([1, 2, 3, 4, 5])->map(
            fn ($rating) => $user->reviews()
                ->where('rating', $rating)
                ->count()
        );

        $topRatedBooks = $user->reviews()
            ->where('rating', '>=', 4)
            ->with('book')
            ->orderBy('rating', 'desc')
            ->get()
            ->map(function ($review) {
                return [
                    'id' => $review->book->id,
                    'title' => $review->book->title,
                    'author' => $review->book->author,
                    'rating' => $review->rating,
                ];
            })
            ->take(5);

        $genreRatings = $user->reviews()
            ->with('book.genres')
            ->get()
            ->flatMap(function ($review) {
                return $review->book->genres->map(function ($genre) use ($review) {
                    return [
                        'id' => $genre->id,
                        'name' => $genre->name,
                        'rating' => $review->rating,
                    ];
                });
            })
            ->groupBy('id')
            ->map(function ($reviews) {
                return [
                    'id' => $reviews->first()['id'],
                    'name' => $reviews->first()['name'],
                    'count' => $reviews->count(),
                    'average_rating' => $reviews->avg('rating'),
                ];
            })
            ->sortByDesc('average_rating')
            ->take(5)
            ->values();

        $stats = [
            'summary' => $summary,
            'rating_distribution' => $ratingDistribution,
            'top_rated_books' => $topRatedBooks,
            'genre_ratings' => $genreRatings,
        ];

        return view('reports.index', compact('stats'));
    }
}