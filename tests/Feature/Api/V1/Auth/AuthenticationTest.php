<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

describe('Authentication API', function () {
    test('user can register successfully', function () {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'title' => 'MR',
            'email' => 'john@example.com',
            'dob' => '1990-01-01',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                    ],
                    'access_token',
                    'token_type',
                ],
            ]);

        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    });

    test('registration fails with invalid data', function () {
        $response = $this->postJson('/api/v1/auth/register', [
            'first_name' => '',
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422);
    });

    test('user can login successfully', function () {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user',
                    'access_token',
                    'token_type',
                ],
            ]);
    });

    test('login fails with invalid credentials', function () {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    });

    test('authenticated user can retrieve their profile', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('data.email', $user->email);
    });

    test('unauthenticated user cannot retrieve profile', function () {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    });

    test('user can logout successfully', function () {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(200);
        $this->assertEmpty($user->tokens);
    });
});
