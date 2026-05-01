<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'event', 'auditable_type', 'auditable_id', 'description', 'metadata',
    ];

    protected $casts = ['metadata' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(string $event, Model $model, string $description, array $metadata = []): void
    {
        static::create([
            'user_id'        => auth()->id(),
            'event'          => $event,
            'auditable_type' => class_basename($model),
            'auditable_id'   => $model->getKey(),
            'description'    => $description,
            'metadata'       => $metadata ?: null,
        ]);
    }
}
