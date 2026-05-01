<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DocumentRequest;
use App\Models\RequestAttachment;
use App\Models\Setting;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRequest::with('attachments', 'assignedTo', 'fulfilledBy')
            ->select('id', 'user_id', 'assigned_to', 'fulfilled_by_document_id', 'title', 'category', 'priority', 'department', 'description', 'status', 'deadline', 'created_at', 'number')
            ->where('user_id', auth()->id())
            ->latest();

        if ($request->filled('status'))   $query->where('status',   $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('search'))   $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%');
        });

        $requests    = $query->paginate(5)->withQueryString();
        $departments = Setting::getGroup('departments');
        $categories  = Setting::getGroup('categories');
        $priorities  = Setting::getGroup('priorities');

        return view('myrequests', compact('requests', 'departments', 'categories', 'priorities'));
    }

    public function store(Request $request)
    {
        $validCategories = array_map('strtolower', Setting::getGroup('categories'));
        $validPriorities = array_map('strtolower', Setting::getGroup('priorities'));

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => ['required', 'string', 'in:' . implode(',', $validCategories)],
            'priority'    => ['required', 'string', 'in:' . implode(',', $validPriorities)],
            'department'  => ['required', 'string', function ($attr, $value, $fail) {
                if ($value === auth()->user()->department) {
                    $fail('You cannot submit a request to your own department.');
                }
            }],
            'assigned_to' => 'required|integer|exists:users,id',
            'description' => 'nullable|string|max:2000',
            'deadline'    => 'required|date_format:Y-m-d\TH:i|after:now',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf',
        ], [
            'title.required' => 'Document title is required.',
            'assigned_to.required' => 'Please select a person to assign this request to.',
            'assigned_to.exists' => 'The selected assignee is invalid.',
            'deadline.after' => 'Deadline must be a future date.',
            'category.in' => 'Please select a valid category.',
            'priority.in' => 'Please select a valid priority.',
        ]);

        $docRequest = DocumentRequest::create([
            'user_id'     => auth()->id(),
            'assigned_to' => $data['assigned_to'] ?? null,
            'title'       => $data['title'],
            'category'    => $data['category'],
            'priority'    => $data['priority'],
            'department'  => $data['department'],
            'description' => $data['description'] ?? null,
            'deadline'    => $data['deadline'] ?? null,
            'status'      => 'pending',
            'number'      => (DocumentRequest::where('department', $data['department'])->max('number') ?? 0) + 1,
        ]);

        // Send notification to assigned user
        if ($data['assigned_to']) {
            NotificationService::notifyRequestAssigned(
                $data['assigned_to'],
                $data['title'],
                str_pad($docRequest->id, 4, '0', STR_PAD_LEFT)
            );
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('request-attachments', 'public');
                RequestAttachment::create([
                    'document_request_id' => $docRequest->id,
                    'filename'            => $file->getClientOriginalName(),
                    'path'                => $path,
                    'size'                => $file->getSize(),
                ]);
            }
        }

        AuditLog::record('request_created', $docRequest,
            "Request {$docRequest->formattedId()} '{$docRequest->title}' created by " . auth()->user()->first_name . ' ' . auth()->user()->last_name,
            ['priority' => $docRequest->priority, 'department' => $docRequest->department]
        );

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Request submitted successfully.']);
        }

        return back()->with('success', 'Request submitted successfully.');
    }

    public function update(Request $request, DocumentRequest $documentRequest)
    {
        abort_if($documentRequest->user_id !== auth()->id(), 403);
        abort_if($documentRequest->status !== 'pending', 403);

        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string',
            'priority'    => 'required|string',
            'department'  => ['required', 'string', function ($attr, $value, $fail) {
                if ($value === auth()->user()->department) {
        // Send notification if assigned_to changed
        if ($data['assigned_to'] !== $documentRequest->assigned_to) {
            NotificationService::notifyRequestAssigned(
                $data['assigned_to'],
                $data['title'],
                str_pad($documentRequest->id, 4, '0', STR_PAD_LEFT)
            );
        }

                    $fail('You cannot submit a request to your own department.');
                }
            }],
            'assigned_to' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'deadline'    => 'required|date',
        ]);

        $documentRequest->update($data);

        return response()->json(['message' => 'Request updated successfully.']);
    }

    public function viewAttachment(RequestAttachment $attachment)
    {
        // Allow access if user owns the request or is assigned to it
        $req = $attachment->request;
        abort_if($req->user_id !== auth()->id() && $req->assigned_to !== auth()->id(), 403);

        abort_if(!Storage::disk('public')->exists($attachment->path), 404);

        return Storage::disk('public')->response($attachment->path, $attachment->filename, [
            'Content-Disposition' => 'inline; filename="' . $attachment->filename . '"',
        ]);
    }

    public function destroy(DocumentRequest $documentRequest)
    {
        abort_if($documentRequest->user_id !== auth()->id(), 403);

        // Delete stored files
        foreach ($documentRequest->attachments as $attachment) {
            \Storage::disk('public')->delete($attachment->path);
        }

        $label = $documentRequest->formattedId();
        $title = $documentRequest->title;
        AuditLog::record('request_deleted', $documentRequest,
            "Request {$label} '{$title}' deleted by " . auth()->user()->first_name . ' ' . auth()->user()->last_name
        );

        $documentRequest->delete();
        DocumentRequest::renumber();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Request deleted.']);
        }

        return back()->with('success', 'Request deleted.');
    }
}
