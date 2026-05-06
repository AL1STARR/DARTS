<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'value', 'is_protected', 'meta'];

    protected $casts = ['is_protected' => 'boolean'];

    public static function getGroup(string $group): array
    {
        return static::where('group', $group)->pluck('value')->toArray();
    }
}
