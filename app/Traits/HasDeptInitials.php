<?php

namespace App\Traits;

trait HasDeptInitials
{
    protected static function deptInitials(string $department): string
    {
        $words    = preg_split('/[\s\-_]+/', trim($department));
        $initials = '';
        foreach ($words as $word) {
            if ($word !== '') $initials .= strtoupper($word[0]);
        }
        return $initials ?: 'XX';
    }
}
