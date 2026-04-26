<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'value'];

    public static function getGroup(string $group): array
    {
        return static::where('group', $group)->pluck('value')->toArray();
    }
}
