<?php

namespace App\Http\Controllers;

use App\Models\DocumentRequest;
use App\Models\RequestAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRequest::with('attachments')
            ->where('user_id', auth()->id())
            ->latest();

        if ($request->filled('status'))   $query->where('status',   $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('search'))   $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
              ->orWhereRaw('CONCAT("REQ-", LPAD(id, 3, "0")) LIKE ?', ['%' . $request->search . '%']);
        });

        $requests = $query->paginate(5)->withQueryString();

        return view('myrequests', compact('requests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'category'    => 'required|string',
            'priority'    => 'required|string',
            'department'  => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'description' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:pdf',
        ]);

        $docRequest = DocumentRequest::create([
            'user_id'     => auth()->id(),
            'assigned_to' => $data['assigned_to'] ?? null,
            'title'       => $data['title'],
            'category'    => $data['category'],
            'priority'    => $data['priority'],
            'department'  => $data['department'],
            'description' => $data['description'] ?? null,
            'status'      => 'pending',
        ]);

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

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Request submitted successfully.']);
        }

        return back()->with('success', 'Request submitted successfully.');
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

        $documentRequest->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Request deleted.']);
        }

        return back()->with('success', 'Request deleted.');
    }
}
