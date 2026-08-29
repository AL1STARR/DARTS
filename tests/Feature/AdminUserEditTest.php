<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_user_without_changing_password(): void
    {
        Setting::ensureDefaults();

        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'Admin',
            'department' => 'Information Technology',
            'is_admin' => true,
            'status' => 'active',
        ]);

        $user = User::factory()->create([
            'email' => 'employee@example.com',
            'first_name' => 'Old',
            'last_name' => 'Name',
            'role' => 'User',
            'department' => 'Human Resources',
            'status' => 'active',
        ]);

        $this->actingAs($admin);

        $response = $this->putJson("/admin/users/{$user->id}", [
            'first_name' => 'New',
            'last_name' => 'Name',
            'email' => 'employee@example.com',
            'role' => 'User',
            'department' => 'Human Resources',
            'status' => 'active',
        ]);

        $response->assertOk();
        $this->assertTrue(
            User::find($user->id)->password !== ''
        );
        $this->assertSame('New', User::find($user->id)->first_name);
    }
}
