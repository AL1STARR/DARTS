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

        $recentRequests = DocumentRequest::where('user_id', $userId)
            ->latest()
            ->take(3)
            ->get();

        return view('index', compact(
            'totalRequests',
            'assignedRequests',
            'departmentArchive',
            'generalArchive',
            'recentRequests'
        ));
    }
}
