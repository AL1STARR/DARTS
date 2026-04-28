<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RouteStage extends Model
{
    protected $fillable = [
        'document_route_id', 'stage_order', 'origin_department', 'waypoint_department', 'handler_id', 'status', 'duration', 'received_at',
    ];

    protected $casts = ['received_at' => 'datetime'];

    public function documentRoute(): BelongsTo
    {
        return $this->belongsTo(DocumentRoute::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handler_id');
    }
}

