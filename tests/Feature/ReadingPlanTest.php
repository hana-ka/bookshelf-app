<?php

namespace Tests\Feature;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_reading_plans(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'title' => 'Laravel入門',
        ]);

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $response = $this->actingAs($user)
            ->get('/reading-plans');

        $response->assertStatus(200);
        $response->assertSee('Laravel入門');
    }

    public function test_user_can_filter_reading_plans_by_status(): void
    {
        $user = User::factory()->create();

        $inProgressBook = Book::factory()->create([
            'title' => '読書中の本',
        ]);

        $completedBook = Book::factory()->create([
            'title' => '読了した本',
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $inProgressBook->id,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $completedBook->id,
            'status' => ReadingPlanStatus::Completed,
        ]);

        $response = $this->actingAs($user)
            ->get('/reading-plans?status=in_progress');

        $response->assertStatus(200);
        $response->assertSee('読書中の本');
        $response->assertDontSee('読了した本');
    }

    public function test_authenticated_user_can_create_reading_plan(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $response = $this->actingAs($user)
            ->post('/reading-plans', [
                'book_id' => $book->id,
                'target_date' => '2026-12-31',
            ]);

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => '2026-12-31',
            'status' => ReadingPlanStatus::InProgress->value,
            'completed_at' => null,
        ]);
    }

    public function test_cannot_create_duplicate_reading_plan_for_same_book(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $response = $this->actingAs($user)
            ->post('/reading-plans', [
                'book_id' => $book->id,
                'target_date' => '2026-12-31',
            ]);

        $response->assertSessionHasErrors('book_id');

        $this->assertDatabaseCount('reading_plans', 1);
    }

    public function test_user_can_complete_reading_plan(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'completed_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->post("/reading-plans/{$readingPlan->id}/complete");

        $response->assertRedirect('/reading-plans');

        $readingPlan->refresh();

        $this->assertSame(
            ReadingPlanStatus::Completed,
            $readingPlan->status
        );

        $this->assertNotNull($readingPlan->completed_at);
    }

    public function test_completed_reading_plan_cannot_be_completed_again(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::Completed,
        ]);

        $response = $this->actingAs($user)
            ->post("/reading-plans/{$readingPlan->id}/complete");

        $response->assertStatus(403);
    }

    public function test_user_can_edit_own_reading_plan(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => '2026-12-01',
        ]);

        $response = $this->actingAs($user)
            ->get("/reading-plans/{$readingPlan->id}/edit");

        $response->assertStatus(200);
    }

    public function test_user_can_update_reading_plan_target_date(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => '2026-12-01',
        ]);

        $response = $this->actingAs($user)
            ->put("/reading-plans/{$readingPlan->id}", [
                'target_date' => '2026-12-31',
            ]);

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'target_date' => '2026-12-31',
        ]);
    }

    public function test_cannot_update_reading_plan_to_past_date(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => '2026-12-01',
        ]);

        $response = $this->actingAs($user)
            ->put("/reading-plans/{$readingPlan->id}", [
                'target_date' => '2020-01-01',
            ]);

        $response->assertSessionHasErrors('target_date');

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'target_date' => '2026-12-01',
        ]);
    }

    public function test_user_cannot_edit_another_users_reading_plan(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $response = $this->actingAs($otherUser)
            ->get("/reading-plans/{$readingPlan->id}/edit");

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_reading_plan(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
            ->delete("/reading-plans/{$readingPlan->id}");

        $response->assertRedirect('/reading-plans');

        $this->assertDatabaseMissing('reading_plans', [
            'id' => $readingPlan->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_reading_plan(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($otherUser)
            ->delete("/reading-plans/{$readingPlan->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
        ]);
    }

    public function test_guest_cannot_access_reading_plans(): void
    {
        $response = $this->get('/reading-plans');

        $response->assertRedirect('/login');
    }
}