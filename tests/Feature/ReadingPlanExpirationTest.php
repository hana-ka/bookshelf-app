<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanExpirationTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_reading_plan_is_automatically_marked_as_expired(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => now()->subDay()->toDateString(),
        ]);

        $this->artisan('reading-plans:process')
            ->assertExitCode(0);

        $readingPlan->refresh();

        $this->assertSame(
            ReadingPlanStatus::Expired,
            $readingPlan->status
        );
    }
}
