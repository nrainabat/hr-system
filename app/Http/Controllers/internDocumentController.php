<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternDocument;
use Illuminate\Support\Facades\Auth;

class InternDocumentController extends Controller
{
    public function index()
    {
        $documents = InternDocument::where('user_id', Auth::id())
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('intern.documents.index', compact('documents'));
    }

    public function create()
    {
        return view('intern.documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'filename' => 'required|string|max:255', // <--- Validating the new input
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'description' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            
            // Keep the original extension for the file storage, but use the Custom Name for the DB
            $originalName = $file->getClientOriginalName();
            $path = $file->storeAs('intern_docs', time() . '_' . $originalName, 'public');

            InternDocument::create([
                'user_id' => Auth::id(),
                'filename' => $request->filename, // <--- SAVING USER INPUT HERE
                'file_path' => $path,
                'description' => $request->description,
                'status' => 'pending',
            ]);

            return redirect()->route('intern.documents.index')
                             ->with('success', 'Document uploaded successfully!');
        }

        return back()->with('error', 'File upload failed.');
    }

    // 4. Show the Review Page (For Supervisors)
    public function edit($id)
    {
        // Ensure only supervisors can access
        if (Auth::user()->role !== 'supervisor') {
            abort(403, 'Unauthorized action.');
        }

        $document = InternDocument::with('user')->findOrFail($id);
        return view('supervisor.documents.review', compact('document'));
    }

    // 5. Process the Review (Sign/Reject/Comment)
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'supervisor') {
            abort(403, 'Unauthorized action.');
        }

        $document = InternDocument::findOrFail($id);

        $request->validate([
            'status' => 'required|in:signed,rejected',
            'supervisor_comment' => 'nullable|string',
            'signed_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        // Handle Signed File Upload
        if ($request->hasFile('signed_file')) {
            $file = $request->file('signed_file');
            $filename = 'SIGNED_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('intern_docs/signed', $filename, 'public');
            $document->signed_file_path = $path;
        }

        $document->status = $request->status;
        $document->supervisor_comment = $request->supervisor_comment;
        $document->save();

        return redirect()->route('dashboard')->with('success', 'Document reviewed successfully.');
    }

    // 6. Show List of All Intern Documents (For Supervisor Navigation)
    public function supervisorIndex()
    {
        if (Auth::user()->role !== 'supervisor') {
            abort(403, 'Unauthorized action.');
        }

        // Fetch all documents, newest first
        $documents = InternDocument::with('user')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('supervisor.documents.index', compact('documents'));
    }
}