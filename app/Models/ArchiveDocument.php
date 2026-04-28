<?php

namespace App\Models;

use App\Traits\HasDeptInitials;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchiveDocument extends Model
{
    use HasDeptInitials;

    protected $fillable = [
        'uploaded_by', 'title', 'description', 'category', 'department',
        'archive_type', 'filename', 'path', 'file_type', 'size', 'number',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function formattedId(): string
    {
        $initials = self::deptInitials($this->department ?? '');
        $num      = str_pad((string) ($this->number ?? $this->id), 4, '0', STR_PAD_LEFT);
        return "DOC-{$initials}-{$num}";
    }

    public static function renumber(): void
    {
        $departments = static::distinct()->pluck('department');
        foreach ($departments as $dept) {
            $i = 1;
            static::where('department', $dept)->orderBy('id')->each(function ($doc) use (&$i) {
                $doc->updateQuietly(['number' => $i++]);
            });
        }
    }
}
