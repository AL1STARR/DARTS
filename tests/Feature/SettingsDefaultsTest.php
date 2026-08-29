<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsDefaultsTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_role_and_department_settings_are_created_when_missing(): void
    {
        Setting::query()->delete();

        $roles = Setting::getGroup('roles');
        $departments = Setting::getGroup('departments');

        $this->assertNotEmpty($roles);
        $this->assertNotEmpty($departments);
        $this->assertContains('Admin', $roles);
        $this->assertContains('Human Resources', $departments);
    }
}
