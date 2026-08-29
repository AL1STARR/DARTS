<?php

namespace App\Models;

use App\Traits\HasDeptInitials;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentRoute extends Model
{
    use HasDeptInitials;

    protected $fillable = [
        'user_id', 'title', 'status', 'priority', 'origin_department',
        'current_waypoint', 'deadline', 'remarks', 'returned_by_department', 'number',
    ];

    protected $casts = ['deadline' => 'datetime'];

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
        $initials = self::deptInitials($this->origin_department ?? '');
        $num      = str_pad((string) ($this->number ?? $this->id), 3, '0', STR_PAD_LEFT);
        return "RT-{$initials}-{$num}";
    }

    public static function renumber(): void
    {
        // Re-number per origin_department independently
        $departments = static::distinct()->pluck('origin_department');
        foreach ($departments as $dept) {
            $i = 1;
            static::where('origin_department', $dept)->orderBy('id')->each(function ($route) use (&$i) {
                $route->updateQuietly(['number' => $i++]);
            });
        }
    }
}
