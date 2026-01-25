<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('authenticated user can store a new address', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/addresses', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'address_line_1' => '123 Street',
            'country' => 'USA',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'is_default' => true,
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('status', 'success')
        ->assertJsonPath('message', 'Address stored successfully.')
        ->assertJsonPath('data.first_name', 'John');

    $this->assertDatabaseHas('addresses', [
        'user_id' => $user->id,
        'first_name' => 'John',
        'is_default' => true,
    ]);
});

test('guest cannot store an address', function () {
    $response = $this->postJson('/api/v1/addresses', [
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $response->assertUnauthorized();
});

test('storing a new default address resets old default address', function () {
    $user = User::factory()->create();
    $oldDefault = Address::create([
        'user_id' => $user->id,
        'first_name' => 'Old',
        'last_name' => 'Default',
        'phone' => '000',
        'address_line_1' => 'Old St',
        'country' => 'Old land',
        'city' => 'Old City',
        'state' => 'Old State',
        'is_default' => true,
    ]);

    $response = $this->actingAs($user)
        ->postJson('/api/v1/addresses', [
            'first_name' => 'New',
            'last_name' => 'Default',
            'phone' => '111',
            'address_line_1' => 'New St',
            'country' => 'New land',
            'city' => 'New City',
            'state' => 'New State',
            'is_default' => true,
        ]);

    $response->assertSuccessful();

    $this->assertTrue($oldDefault->fresh()->is_default === false);
    $this->assertDatabaseHas('addresses', [
        'first_name' => 'New',
        'is_default' => true,
    ]);
});

test('validation errors for missing required fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/addresses', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['first_name', 'last_name', 'phone', 'address_line_1', 'country', 'city', 'state']);
});
