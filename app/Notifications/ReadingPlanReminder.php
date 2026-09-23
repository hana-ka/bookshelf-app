<?php

namespace App\Notifications;

use App\Models\ReadingPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReadingPlanReminder extends Notification
{
    use Queueable;

    public function __construct(
        public ReadingPlan $readingPlan,
        public string $timing,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $title = match ($this->timing) {
            'three_days_before' => '読書計画の期日まであと3日',
            'on_due_date' => '読書計画の期日です',
            'three_days_after' => '読書計画の期日を過ぎています',
        };

        return [
            'reading_plan_id' => $this->readingPlan->id,
            'timing' => $this->timing,
            'title' => $title,
            'body' => $this->readingPlan->book->title,
        ];
    }
}