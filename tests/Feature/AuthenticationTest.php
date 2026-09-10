<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_unregistered_email()
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'notfound@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_cannot_login_without_required_fields()
    {
        $response = $this->from('/login')->post('/login', []);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email',
            'password',
        ]);
        $this->assertGuest();
    }

    public function test_user_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);
        $this->assertAuthenticated();
    }

    public function test_user_cannot_register_with_duplicate_email()
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->from('/register')->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_cannot_register_with_mismatched_password_confirmation()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'wrong-password',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_user_cannot_register_without_required_fields()
    {
        $response = $this->from('/register')->post('/register', []);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'name',
            'email',
            'password',
        ]);
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_from_login_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/');
    }

    public function test_authenticated_user_is_redirected_from_register_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect('/');
    }
}