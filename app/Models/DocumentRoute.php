<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentRoute extends Model
{
    protected $fillable = [
        'user_id', 'title', 'status', 'priority', 'origin_department', 'current_waypoint',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(RouteStage::class)->orderBy('stage_order');
    }

    public function currentStage(): ?RouteStage
    {
        return $this->stages()->where('status', 'active')->first()
            ?? $this->stages()->where('status', 'pending')->first();
    }

    public function formattedId(): string
    {
        return 'RT-' . str_pad((string) $this->id, 3, '0', STR_PAD_LEFT);
    }
}

