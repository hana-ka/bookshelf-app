<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;

class ReviewLikeController extends Controller
{
    public function toggle(Review $review)
    {
        $user = Auth::user();

        if($user->likedReviews->contains($review->id)){
            $user->likedReviews()->detach($review->id);
        }else{
                $user->likedReviews()->attach($review->id);
        }

        return redirect()->route('books.show', $review->book);
    }
}
