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
}