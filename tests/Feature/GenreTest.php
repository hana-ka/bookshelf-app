<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    public function test_genre_can_be_created()
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'テストジャンル',
        ];

        $response = $this->actingAs($user)
            ->post('/genres', $data);

        $response->assertStatus(302);

        $this->assertDatabaseHas('genres', [
            'name' => 'テストジャンル',
        ]);
    }

    public function test_genre_creation_redirects_to_index_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/genres', [
                'name' => 'テストジャンル',
            ]);

        $response->assertRedirect(route('genres.index'));
    }

    public function test_genre_creation_displays_success_message()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/genres', [
                'name' => 'テストジャンル',
            ]);

        $response->assertSessionHas('success', 'ジャンルを作成しました。');
    }

    public function test_genre_creation_validation_fails_with_invalid_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/genres', [
                'name' => '',
            ]);

        $response->assertSessionHasErrors([
            'name' => 'ジャンル名を入力してください。',
        ]);
    }

    public function test_genre_creation_fails_with_duplicate_name()
    {
        $user = User::factory()->create();

        Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);

        $response = $this->actingAs($user)
            ->post('/genres', [
                'name' => 'テストジャンル',
            ]);

        $response->assertSessionHasErrors([
            'name' => 'このジャンル名は既に登録されています。',
        ]);
    }

    public function test_guest_cannot_view_genre_create_page()
    {
        $response = $this->get('/genres/create');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_create_genre()
    {
        $response = $this->post('/genres', [
            'name' => 'ゲストジャンル',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('genres', [
            'name' => 'ゲストジャンル',
        ]);
    }

    public function test_genre_can_be_updated()
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create([
            'name' => '更新前ジャンル',
        ]);

        $response = $this->actingAs($user)
            ->put("/genres/{$genre->id}", [
                'name' => '更新後ジャンル',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => '更新後ジャンル',
        ]);
    }

    public function test_genre_update_redirects_to_index_page()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)
            ->put("/genres/{$genre->id}", [
                'name' => '更新後ジャンル',
            ]);

        $response->assertRedirect(route('genres.index'));
    }

    public function test_genre_update_displays_success_message()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)
            ->put("/genres/{$genre->id}", [
                'name' => '更新後ジャンル',
            ]);

        $response->assertSessionHas('success', 'ジャンルを更新しました。');
    }

    public function test_genre_update_validation_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)
            ->put("/genres/{$genre->id}", [
                'name' => '',
            ]);

        $response->assertSessionHasErrors([
            'name' => 'ジャンル名を入力してください。',
        ]);
    }

    public function test_genre_update_fails_with_duplicate_name()
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create([
            'name' => '更新対象ジャンル',
        ]);

        Genre::factory()->create([
            'name' => '既存ジャンル',
        ]);

        $response = $this->actingAs($user)
            ->put("/genres/{$genre->id}", [
                'name' => '既存ジャンル',
            ]);

        $response->assertSessionHasErrors([
            'name' => 'このジャンル名は既に登録されています。',
        ]);
    }

    public function test_genre_can_be_updated_with_its_own_name()
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);

        $response = $this->actingAs($user)
            ->put("/genres/{$genre->id}", [
                'name' => 'テストジャンル',
            ]);

        $response->assertSessionDoesntHaveErrors();
    }

    public function test_guest_cannot_view_genre_edit_page()
    {
        $genre = Genre::factory()->create();

        $response = $this->get("/genres/{$genre->id}/edit");

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_update_genre()
    {
        $genre = Genre::factory()->create([
            'name' => '更新前ジャンル',
        ]);

        $response = $this->put("/genres/{$genre->id}", [
            'name' => 'ゲスト更新ジャンル',
        ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => '更新前ジャンル',
        ]);
    }

    public function test_genre_can_be_deleted()
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)
            ->delete("/genres/{$genre->id}");

        $response->assertStatus(302);

        $this->assertDatabaseMissing('genres', [
            'id' => $genre->id,
        ]);
    }

    public function test_genre_deletion_redirects_to_index_page()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)
            ->delete("/genres/{$genre->id}");

        $response->assertRedirect(route('genres.index'));
    }

    public function test_genre_deletion_displays_success_message()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)
            ->delete("/genres/{$genre->id}");

        $response->assertSessionHas('success', 'ジャンルを削除しました。');
    }

    public function test_genre_with_books_cannot_be_deleted()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        $book->genres()->attach($genre);

        $response = $this->actingAs($user)
            ->delete("/genres/{$genre->id}");

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
        ]);
    }

    public function test_guest_cannot_delete_genre()
    {
        $genre = Genre::factory()->create();

        $response = $this->delete("/genres/{$genre->id}");

        $response->assertRedirect('/login');

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
        ]);
    }
}