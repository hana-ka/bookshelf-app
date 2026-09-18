<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReadingPlan;
use Illuminate\Support\Facades\Auth;
use App\Enums\ReadingPlanStatus;
use Carbon\Carbon;
use App\Models\Book;
use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;


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

    public function complete(ReadingPlan $Plan)
    {
        abort_unless($Plan->user_id === Auth::id(), 403);

        if ($Plan->status === ReadingPlanStatus::Completed) {
            abort(403);
        }

        $Plan->update([
            'status' => ReadingPlanStatus::Completed,
            'completed_at' => Carbon::now(),
        ]);

        return redirect()->route('reading-plans.index');
    }

    public function create()
    {
        $books = Book::all();

        return view('reading-plans.create', compact('books'));
    }

    public function store(StoreReadingPlanRequest $request)
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

    public function update(UpdateReadingPlanRequest $request, ReadingPlan $plan)
    {
        abort_unless($plan->user_id === Auth::id(), 403);

        if ($plan->status === ReadingPlanStatus::Completed) {
            abort(403);
        }

        $plan->update([
            'target_date' => $request->target_date,
        ]);

        return redirect()->route('reading-plans.index');
    }

    public function edit(ReadingPlan $plan)
    {
        abort_unless($plan->user_id === Auth::id(), 403);

        if ($plan->status === ReadingPlanStatus::Completed) {
            abort(403);
        }

        return view('reading-plans.edit', ['readingPlan' => $plan,]);
    }

    public function destroy(ReadingPlan $plan)
    {
        abort_unless($plan->user_id === Auth::id(), 403);

        $plan->delete();

        return redirect()->route('reading-plans.index');
    }
}
