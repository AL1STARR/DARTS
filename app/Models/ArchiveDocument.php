<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArchiveDocument extends Model
{
    protected $fillable = [
        'uploaded_by', 'title', 'category', 'department',
        'archive_type', 'filename', 'path', 'file_type', 'size',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
