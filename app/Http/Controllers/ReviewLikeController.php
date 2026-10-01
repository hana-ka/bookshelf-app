<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewLikeController extends Controller
{
    /**
     * Toggle the authenticated user's like status for the specified review.
     */
    public function toggle(Review $review): RedirectResponse
    {
        $user = Auth::user();

        if ($user->likedReviews->contains($review->id)) {
            $user->likedReviews()->detach($review->id);
        } else {
            $user->likedReviews()->attach($review->id);
        }

        return redirect()->route('books.show', $review->book);
    }
}
