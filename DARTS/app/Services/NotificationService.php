<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function create($userId, $title, $description)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
        ]);
    }

    public static function notifyRequestAssigned($assignedToUserId, $requestTitle, $requestId)
    {
        return self::create(
            $assignedToUserId,
            'New Assignment',
            "You have been assigned to review #{$requestId}: {$requestTitle}."
        );
    }

    public static function notifyRequestStatusChanged($userId, $status, $requestId, $requestTitle)
    {
        $statusLabel = match($status) {
            'in-review' => 'In Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => ucfirst(str_replace('-', ' ', $status))
        };

        return self::create(
            $userId,
            "Request {$statusLabel}",
            "Request #{$requestId} ({$requestTitle}) has been {$statusLabel}."
        );
    }

    public static function notifyUserApproved($userId, $userName)
    {
        return self::create(
            $userId,
            'Access Approved',
            "Your account access has been approved by an administrator."
        );
    }
}
