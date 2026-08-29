<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create roles
        $roles = ['Admin', 'User', 'Manager'];
        foreach ($roles as $role) {
            Setting::firstOrCreate(
                ['group' => 'roles', 'value' => $role],
                ['is_protected' => true]
            );
        }

        // Create categories
        $categories = ['Reports', 'Contracts', 'Policies', 'Compliance', 'Financial', 'HR'];
        foreach ($categories as $category) {
            Setting::firstOrCreate(
                ['group' => 'categories', 'value' => $category],
                ['is_protected' => true]
            );
        }

        // Create priorities
        $priorities = ['High', 'Medium', 'Low'];
        foreach ($priorities as $priority) {
            Setting::firstOrCreate(
                ['group' => 'priorities', 'value' => $priority],
                ['is_protected' => true]
            );
        }

        // Create departments
        $departments = ['Human Resources', 'Finance', 'Information Technology', 'Operations', 'Legal'];
        foreach ($departments as $department) {
            Setting::firstOrCreate(
                ['group' => 'departments', 'value' => $department],
                ['is_protected' => true]
            );
        }
    }
}
