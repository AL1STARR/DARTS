<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\ArchiveDocument;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $totalRequests     = DocumentRequest::where('user_id', $userId)->count();
        $assignedRequests  = DocumentRequest::where('assigned_to', $userId)->count();
        $departmentArchive = ArchiveDocument::where('archive_type', 'department')
                                ->where('department', auth()->user()->department)->count();
        $generalArchive    = ArchiveDocument::where('archive_type', 'general')->count();

        $recentRequests = DocumentRequest::where('assigned_to', $userId)
            ->whereIn('status', ['pending', 'in-review'])
            ->latest()
            ->take(3)
            ->get();

        // 2 nearest deadline requests assigned to the user that are not done
        $nearDeadlineRequests = DocumentRequest::where('assigned_to', $userId)
            ->whereNotNull('deadline')
            ->whereNotIn('status', ['approved', 'rejected'])
            ->where('deadline', '>=', now())
            ->orderBy('deadline', 'asc')
            ->take(2)
            ->get();

        return view('index', compact(
            'totalRequests',
            'assignedRequests',
            'departmentArchive',
            'generalArchive',
            'recentRequests',
            'nearDeadlineRequests'
        ));
    }
}
