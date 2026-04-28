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
        Setting::firstOrCreate(
            ['group' => 'roles', 'value' => 'Admin'],
            ['is_protected' => true]
        );
    }
}
