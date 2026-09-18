<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReadingPlan;
use Illuminate\Support\Facades\Auth;


class ReadingPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()
            ->readingPlans()
            ->with('book');

        $currentStatus = $request->input('status');

        if ($currentStatus) {
            $query->where('status', $currentStatus);
        }

        $readingPlans = $query->get();

        return view('reading-plans.index', compact(
            'readingPlans',
            'currentStatus'
        ));
    }
}
