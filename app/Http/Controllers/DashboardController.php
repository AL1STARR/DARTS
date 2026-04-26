<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $totalRequests    = DocumentRequest::where('user_id', $userId)->count();
        $assignedRequests = 0;  // placeholder until assigned requests table exists
        $departmentArchive = 0; // placeholder until archive table exists
        $generalArchive    = 0; // placeholder until archive table exists

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
