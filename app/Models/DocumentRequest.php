<?php

namespace App\Models;

use App\Traits\HasDeptInitials;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentRequest extends Model
{
    use HasDeptInitials;

    protected $fillable = [
        'user_id', 'assigned_to', 'fulfilled_by_document_id', 'title', 'category',
        'priority', 'status', 'department', 'description', 'deadline', 'number',
    ];

    protected $casts = ['deadline' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function fulfilledBy(): BelongsTo
    {
        return $this->belongsTo(ArchiveDocument::class, 'fulfilled_by_document_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(RequestAttachment::class);
    }

    public function formattedId(): string
    {
        $initials = self::deptInitials($this->department ?? '');
        $num      = str_pad((string) ($this->number ?? $this->id), 4, '0', STR_PAD_LEFT);
        return "REQ-{$initials}-{$num}";
    }

    public static function renumber(): void
    {
        // Re-number per department independently
        $departments = static::distinct()->pluck('department');
        foreach ($departments as $dept) {
            $i = 1;
            static::where('department', $dept)->orderBy('id')->each(function ($req) use (&$i) {
                $req->updateQuietly(['number' => $i++]);
            });
        }
    }
}
