<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReadingPlanReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_reminder_three_days_before(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => now()->addDays(3)->toDateString(),
        ]);

        $this->artisan('reading-plans:process')
            ->assertExitCode(0);

        Notification::assertSentTo(
            $user,
            ReadingPlanReminder::class,
            function ($notification) {
                return $notification->timing === 'three_days_before';
            }
        );
    }

    public function test_sends_reminder_on_due_date(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => now()->toDateString(),
        ]);

        $this->artisan('reading-plans:process')
            ->assertExitCode(0);

        Notification::assertSentTo(
            $user,
            ReadingPlanReminder::class,
            function ($notification) {
                return $notification->timing === 'on_due_date';
            }
        );
    }

    public function test_sends_reminder_three_days_after_due_date(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::Expired,
            'target_date' => now()->subDays(3)->toDateString(),
        ]);

        $this->artisan('reading-plans:process')
            ->assertExitCode(0);

        Notification::assertSentTo(
            $user,
            ReadingPlanReminder::class,
            function ($notification) {
                return $notification->timing === 'three_days_after';
            }
        );
    }

    public function test_does_not_send_duplicate_reminder(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => now()->addDays(3)->toDateString(),
        ]);

        $user->notify(
            new ReadingPlanReminder(
                $readingPlan,
                'three_days_before'
            )
        );

        $this->assertDatabaseCount('notifications', 1);

        $this->artisan('reading-plans:process')
            ->assertExitCode(0);

        $this->assertDatabaseCount('notifications', 1);
    }
}
