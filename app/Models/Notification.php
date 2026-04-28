<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'read',
        'dismissed',
        'read_at',
        'dismissed_at',
    ];

    protected $casts = [
        'read' => 'boolean',
        'dismissed' => 'boolean',
        'read_at' => 'datetime',
        'dismissed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now(),
        ]);
    }

    public function markAsDismissed()
    {
        $this->update([
            'dismissed' => true,
            'dismissed_at' => now(),
        ]);
    }
}
