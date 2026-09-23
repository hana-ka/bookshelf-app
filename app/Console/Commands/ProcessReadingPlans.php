<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use Carbon\Carbon;
use App\Notifications\ReadingPlanReminder;

class ProcessReadingPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reading-plans:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '読書計画の期限処理とリマインダー通知を実行します';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        $plans = ReadingPlan::whereIn('status', [
            ReadingPlanStatus::InProgress,
            ReadingPlanStatus::Expired,
        ])
            ->with('book', 'user')
            ->get();

        foreach ($plans as $plan) {

            if (
                $plan->status === ReadingPlanStatus::InProgress
                && $plan->target_date->lt($today)
            ) {
                $plan->update([
                    'status' => ReadingPlanStatus::Expired,
                ]);
            }

            if ($plan->target_date->isSameDay($today->copy()->addDays(3))) {

                $alreadyNotified = $plan->user->notifications()
                    ->where('type', ReadingPlanReminder::class)
                    ->where('data->reading_plan_id', $plan->id)
                    ->where('data->timing', 'three_days_before')
                    ->exists();

                if (!$alreadyNotified) {
                    $plan->user->notify(
                        new ReadingPlanReminder(
                            $plan,
                            'three_days_before'
                        )
                    );
                }
            }

            if ($plan->target_date->isSameDay($today)) {

                $alreadyNotified = $plan->user->notifications()
                    ->where('type', ReadingPlanReminder::class)
                    ->where('data->reading_plan_id', $plan->id)
                    ->where('data->timing', 'on_due_date')
                    ->exists();

                if (!$alreadyNotified) {
                    $plan->user->notify(
                        new ReadingPlanReminder(
                            $plan,
                            'on_due_date'
                        )
                    );
                }
            }

            if ($plan->target_date->isSameDay($today->copy()->subDays(3))) {

                $alreadyNotified = $plan->user->notifications()
                    ->where('type', ReadingPlanReminder::class)
                    ->where('data->reading_plan_id', $plan->id)
                    ->where('data->timing', 'three_days_after')
                    ->exists();

                if (!$alreadyNotified) {
                    $plan->user->notify(
                        new ReadingPlanReminder(
                            $plan,
                            'three_days_after'
                        )
                    );
                }
            }
        }

        return Command::SUCCESS;
    }
}