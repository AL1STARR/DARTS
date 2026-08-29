<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'value', 'is_protected', 'meta'];

    protected $casts = ['is_protected' => 'boolean'];

    public static function ensureDefaults(): void
    {
        $defaults = [
            'roles' => ['Admin', 'User', 'Manager', 'Records Officer', 'HR Specialist', 'Finance Officer'],
            'departments' => ['Human Resources', 'Finance', 'Information Technology', 'Operations', 'Legal'],
            'categories' => ['Reports', 'Contracts', 'Policies', 'Compliance', 'Financial', 'HR'],
            'priorities' => ['High', 'Medium', 'Low'],
        ];

        foreach ($defaults as $group => $values) {
            foreach ($values as $value) {
                static::firstOrCreate(
                    ['group' => $group, 'value' => $value],
                    ['is_protected' => true]
                );
            }
        }
    }

    public static function getGroup(string $group): array
    {
        static::ensureDefaults();

        return static::where('group', $group)->pluck('value')->toArray();
    }
}
