<?php

namespace App\Http\Controllers;

use App\Models\ArchiveDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = ArchiveDocument::with('uploader')->latest();

        if ($request->filled('tab'))       $query->where('archive_type', $request->tab);
        if ($request->filled('file_type')) $query->where('file_type',    $request->file_type);
        if ($request->filled('category'))  $query->where('category',     $request->category);
        if ($request->filled('department')) $query->where('department',  $request->department);
        if ($request->filled('search'))    $query->where('title', 'like', '%' . $request->search . '%');

        $documents = $query->paginate(5)->withQueryString();

        return view('archive', compact('documents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string',
            'department'   => 'required|string',
            'archive_type' => 'required|in:general,department',
            'file'         => 'required|file|max:20480|mimes:pdf,doc,docx,xlsx,pptx',
        ]);

        $file      = $request->file('file');
        $path      = $file->store('archive-documents', 'public');
        $fileType  = in_array($file->getClientOriginalExtension(), ['doc', 'docx']) ? 'docs' : $file->getClientOriginalExtension();

        ArchiveDocument::create([
            'uploaded_by'  => auth()->id(),
            'title'        => $data['title'],
            'category'     => $data['category'],
            'department'   => $data['department'],
            'archive_type' => $data['archive_type'],
            'filename'     => $file->getClientOriginalName(),
            'path'         => $path,
            'file_type'    => $fileType,
            'size'         => $file->getSize(),
        ]);

        return response()->json(['message' => 'Document uploaded successfully.']);
    }

    public function download(ArchiveDocument $archiveDocument)
    {
        abort_if(!Storage::disk('public')->exists($archiveDocument->path), 404);

        return Storage::disk('public')->download($archiveDocument->path, $archiveDocument->filename);
    }

    public function destroy(ArchiveDocument $archiveDocument)
    {
        Storage::disk('public')->delete($archiveDocument->path);
        $archiveDocument->delete();

        return response()->json(['message' => 'Document deleted.']);
    }

    public function departmentDocs(Request $request)
    {
        $docs = ArchiveDocument::where('department', $request->query('department'))
            ->latest()
            ->get(['id', 'title', 'file_type', 'category', 'archive_type']);

        return response()->json($docs);
    }
}
