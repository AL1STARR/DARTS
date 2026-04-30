<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class AssignedRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRequest::with(['user', 'assignedTo', 'attachments'])
            ->select('id', 'user_id', 'assigned_to', 'title', 'category', 'priority', 'department', 'description', 'status', 'deadline', 'created_at', 'number')
            ->where('assigned_to', auth()->id())
            ->latest();

        if ($request->filled('status'))   $query->where('status',   $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('search'))   $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhereRaw('CONCAT("REQ-", LPAD(id, 4, "0")) LIKE ?', ['%' . $request->search . '%']);
        });

        // If ?open= is set, find which page that request is on and force that page
        if ($request->filled('open')) {
            $openId  = (int) $request->open;
            $ids     = (clone $query)->pluck('id')->values();
            $pos     = $ids->search($openId);
            if ($pos !== false) {
                $page = (int) floor($pos / 5) + 1;
                $request->merge(['page' => $page]);
            }
        }

        $requests   = $query->paginate(5)->withQueryString();
        $categories = \App\Models\Setting::getGroup('categories');
        $priorities = \App\Models\Setting::getGroup('priorities');

        return view('assigned', compact('requests', 'categories', 'priorities'));
    }

    public function updateStatus(Request $request, DocumentRequest $documentRequest)
    {
        abort_if($documentRequest->assigned_to !== auth()->id(), 403);

        $data = $request->validate([
            'status'                  => 'required|in:in-review,approved,rejected',
            'fulfilled_by_document_id' => 'nullable|exists:archive_documents,id',
        ]);

        $update = ['status' => $data['status']];
        if (array_key_exists('fulfilled_by_document_id', $data)) {
            $update['fulfilled_by_document_id'] = $data['fulfilled_by_document_id'];
        }

        $documentRequest->update($update);

        // Notify request owner of status change
        NotificationService::notifyRequestStatusChanged(
            $documentRequest->user_id,
            $data['status'],
            str_pad($documentRequest->id, 4, '0', STR_PAD_LEFT),
            $documentRequest->title
        );

        return response()->json(['message' => 'Status updated.']);
    }

    public function transfer(Request $request, DocumentRequest $documentRequest)
    {
        abort_if($documentRequest->assigned_to !== auth()->id(), 403);

        $data = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        // Ensure the new assignee is from the same department
        $newAssignee = User::findOrFail($data['assigned_to']);
        abort_if($newAssignee->department !== $documentRequest->department, 422);

        $documentRequest->update(['assigned_to' => $data['assigned_to']]);

        // Notify new assignee
        NotificationService::notifyRequestAssigned(
            $data['assigned_to'],
            $documentRequest->title,
            str_pad($documentRequest->id, 4, '0', STR_PAD_LEFT)
        );

        return response()->json(['message' => 'Request transferred successfully.']);
    }

    public function departmentUsers(Request $request)
    {
        $department = $request->query('department');

        $users = User::where('department', $department)
            ->where('status', 'active')
            ->where('id', '!=', auth()->id())
            ->select('id', 'first_name', 'last_name', 'role')
            ->get();

        return response()->json($users);
    }
}
