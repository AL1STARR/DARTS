<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\ArchiveDocument;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'general');

        $query = ArchiveDocument::with('uploader')->latest();

        // If searching, search across both tabs but still scope department tab to user's dept
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%');
            });
        } else {
            $query->where('archive_type', $tab);
            if ($tab === 'department') {
                $query->where('department', auth()->user()->department);
            }
        }

        if ($request->filled('file_type'))  $query->where('file_type',  $request->file_type);
        if ($request->filled('category'))   $query->where('category',   $request->category);
        if ($request->filled('department')) $query->where('department', $request->department);

        $documents = $query->paginate(5)->withQueryString();

        // Sidebar stats — calculate using database queries instead of fetching all records
        $statsQuery = ArchiveDocument::where('archive_type', $tab);
        if ($tab === 'department') {
            $statsQuery->where('department', auth()->user()->department);
        }

        // Use database aggregation for file type stats
        $fileTypeStats = $statsQuery->select('file_type')->selectRaw('count(*) as count')
            ->groupBy('file_type')
            ->get()
            ->pluck('count', 'file_type');

        // Use database aggregation for distribution stats
        if ($tab === 'general') {
            $distStats = $statsQuery->select('department')->selectRaw('count(*) as count')
                ->groupBy('department')
                ->get()
                ->pluck('count', 'department');
        } else {
            $distStats = ArchiveDocument::where('archive_type', $tab)
                ->where('department', auth()->user()->department)
                ->with('uploader:id,first_name,last_name')
                ->selectRaw('uploaded_by, count(*) as count')
                ->groupBy('uploaded_by')
                ->get()
                ->map(fn($d) => "{$d->uploader->first_name} {$d->uploader->last_name}")
                ->countBy();
        }

        $categories  = Setting::getGroup('categories');
        $departments = Setting::getGroup('departments');

        return view('archive', compact('documents', 'fileTypeStats', 'distStats', 'tab', 'categories', 'departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string',
            'archive_type' => 'required|in:general,department',
            'file'         => 'required|file|max:20480|mimes:pdf,doc,docx,xlsx,pptx',
        ]);

        $file      = $request->file('file');
        $fileType  = in_array($file->getClientOriginalExtension(), ['doc', 'docx']) ? 'docs' : $file->getClientOriginalExtension();
        $path      = $file->store('archive-documents', 'public');

        $dept = auth()->user()->department;

        $doc = ArchiveDocument::create([
            'uploaded_by'  => auth()->id(),
            'title'        => $data['title'],
            'category'     => $data['category'],
            'department'   => $dept,
            'archive_type' => $data['archive_type'],
            'filename'     => $file->getClientOriginalName(),
            'path'         => $path,
            'file_type'    => $fileType,
            'size'         => $file->getSize(),
            'number'       => (ArchiveDocument::where('department', $dept)->max('number') ?? 0) + 1,
        ]);

        AuditLog::record('document_uploaded', $doc,
            "Document {$doc->formattedId()} '{$doc->title}' uploaded by " . auth()->user()->first_name . ' ' . auth()->user()->last_name,
            ['file_type' => $fileType, 'archive_type' => $data['archive_type']]
        );

        return response()->json(['message' => 'Document uploaded successfully.']);
    }

    public function update(Request $request, ArchiveDocument $archiveDocument)
    {
        abort_if($archiveDocument->uploaded_by !== auth()->id(), 403);

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string',
            'archive_type' => 'required|in:general,department',
        ]);

        $archiveDocument->update($data);

        return response()->json(['message' => 'Document updated successfully.']);
    }

    public function view(ArchiveDocument $archiveDocument)
    {
        abort_if(!Storage::disk('public')->exists($archiveDocument->path), 404);

        return Storage::disk('public')->response($archiveDocument->path, $archiveDocument->filename, [
            'Content-Disposition' => 'inline; filename="' . $archiveDocument->filename . '"',
        ]);
    }

    public function download(ArchiveDocument $archiveDocument)
    {
        abort_if(!Storage::disk('public')->exists($archiveDocument->path), 404);

        return Storage::disk('public')->download($archiveDocument->path, $archiveDocument->filename);
    }

    public function destroy(ArchiveDocument $archiveDocument)
    {
        $label = $archiveDocument->formattedId();
        $title = $archiveDocument->title;
        AuditLog::record('document_deleted', $archiveDocument,
            "Document {$label} '{$title}' deleted by " . auth()->user()->first_name . ' ' . auth()->user()->last_name
        );
        Storage::disk('public')->delete($archiveDocument->path);
        $archiveDocument->delete();
        ArchiveDocument::renumber();

        return response()->json(['message' => 'Document deleted.']);
    }

    public function departmentDocs(Request $request)
    {
        $query = ArchiveDocument::where('department', $request->query('department'));

        // Support searching by document ID or title
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('title', 'like', '%' . $search . '%');
        }

        $docs = $query->latest()->with('uploader')->get();

        return response()->json($docs->map(fn($d) => [
            'id'           => $d->id,
            'formatted_id' => $d->formattedId(),
            'title'        => $d->title,
            'file_type'    => $d->file_type,
            'category'     => $d->category,
            'archive_type' => $d->archive_type,
        ]));
    }
}
