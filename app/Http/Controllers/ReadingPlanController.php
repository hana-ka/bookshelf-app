<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadingPlanController extends Controller
{
    /**
     * Display the authenticated user's reading plans.
     */
    public function index(Request $request): View
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

    /**
     * Mark the specified reading plan as completed.
     */
    public function complete(ReadingPlan $plan): RedirectResponse
    {
        $this->authorize('update', $plan);

        if ($plan->status === ReadingPlanStatus::Completed) {
            abort(403);
        }

        $plan->update([
            'status' => ReadingPlanStatus::Completed,
            'completed_at' => Carbon::now(),
        ]);

        return redirect()->route('reading-plans.index');
    }

    /**
     * Show the form for creating a new reading plan.
     */
    public function create(): View
    {
        $books = Book::all();

        return view('reading-plans.create', compact('books'));
    }

    /**
     * Store a newly created reading plan.
     */
    public function store(StoreReadingPlanRequest $request): RedirectResponse
    {
        ReadingPlan::create([
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
            'target_date' => $request->target_date,
            'status' => ReadingPlanStatus::InProgress,
            'completed_at' => null,
        ]);

        return redirect()->route('reading-plans.index');
    }

    /**
     * Update the target date of the specified reading plan.
     */
    public function update(
        UpdateReadingPlanRequest $request,
        ReadingPlan $plan
    ): RedirectResponse {
        $this->authorize('update', $plan);

        if ($plan->status === ReadingPlanStatus::Completed) {
            abort(403);
        }

        $plan->update([
            'target_date' => $request->target_date,
        ]);

        return redirect()->route('reading-plans.index');
    }

    /**
     * Show the form for editing the specified reading plan.
     */
    public function edit(ReadingPlan $plan): View
    {
        $this->authorize('update', $plan);

        if ($plan->status === ReadingPlanStatus::Completed) {
            abort(403);
        }

        return view('reading-plans.edit', [
            'readingPlan' => $plan,
        ]);
    }

    /**
     * Remove the specified reading plan.
     */
    public function destroy(ReadingPlan $plan): RedirectResponse
    {
        $this->authorize('delete', $plan);

        $plan->delete();

        return redirect()->route('reading-plans.index');
    }
}
