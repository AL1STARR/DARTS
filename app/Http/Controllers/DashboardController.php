<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\ArchiveDocument;
use App\Models\DocumentRoute;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function search(Request $request)
    {
        $q    = trim($request->query('q', ''));
        if (strlen($q) < 1) return response()->json([]);

        $userId = auth()->id();
        $dept   = auth()->user()->department;

        // Extract the numeric portion from queries like "0001", "DOC-IT-0001", "REQ-IT-001", "RT-IT-001"
        preg_match('/(\d+)$/', $q, $m);
        $numStr = $m[1] ?? null;

        $results = collect();

        // ── Archive Documents ──
        $docQuery = ArchiveDocument::where(function ($query) use ($dept) {
            $query->where('archive_type', 'general')
                  ->orWhere(fn($q2) => $q2->where('archive_type', 'department')->where('department', $dept));
        });
        $docQuery->where(function ($query) use ($q, $numStr) {
            $query->where('title', 'like', '%' . $q . '%');
            if ($numStr) {
                $query->orWhereRaw('LPAD(CAST(number AS CHAR), 4, "0") LIKE ?', ['%' . $numStr . '%']);
            }
        });
        $docQuery->latest()->limit(5)->get()->each(function ($d) use (&$results) {
            $results->push([
                'type'         => 'document',
                'label'        => 'Archive',
                'formatted_id' => $d->formattedId(),
                'title'        => $d->title,
                'file_type'    => $d->file_type,
                'status'       => null,
                'view_url'     => route('archive.view', $d),
                'download_url' => route('archive.download', $d),
                'page_url'     => route('archive'),
            ]);
        });

        // ── Document Requests (submitted by or assigned to user) ──
        $reqQuery = DocumentRequest::where(fn($q2) => $q2->where('user_id', $userId)->orWhere('assigned_to', $userId));
        $reqQuery->where(function ($query) use ($q, $numStr) {
            $query->where('title', 'like', '%' . $q . '%');
            if ($numStr) {
                $query->orWhereRaw('LPAD(CAST(number AS CHAR), 3, "0") LIKE ?', ['%' . $numStr . '%']);
            }
        });
        $reqQuery->latest()->limit(5)->get()->each(function ($r) use (&$results) {
            $results->push([
                'type'         => 'request',
                'label'        => 'Request',
                'formatted_id' => $r->formattedId(),
                'title'        => $r->title,
                'file_type'    => null,
                'status'       => $r->status,
                'view_url'     => null,
                'download_url' => null,
                'page_url'     => route('myrequests'),
            ]);
        });

        // ── Document Routes ──
        $rtQuery = DocumentRoute::query();
        $rtQuery->where(function ($query) use ($q, $numStr) {
            $query->where('title', 'like', '%' . $q . '%');
            if ($numStr) {
                $query->orWhereRaw('LPAD(CAST(number AS CHAR), 3, "0") LIKE ?', ['%' . $numStr . '%']);
            }
        });
        $rtQuery->latest()->limit(5)->get()->each(function ($r) use (&$results) {
            $results->push([
                'type'         => 'route',
                'label'        => 'Routing',
                'formatted_id' => $r->formattedId(),
                'title'        => $r->title,
                'file_type'    => null,
                'status'       => $r->status,
                'view_url'     => null,
                'download_url' => null,
                'page_url'     => route('routing'),
            ]);
        });

        return response()->json($results->values());
    }

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
