<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $student = Auth::guard('student')->user();

        if ($student->application_status === 'submitted') {
            return back()->with('error', 'Documents cannot be changed after submission.');
        }

        $documentTypes = array_keys(config('student.document_types', []));

        $validated = $request->validate([
            'document_type' => ['required', 'string', Rule::in($documentTypes)],
            'file' => [
                'required',
                'file',
                'mimes:' . implode(',', config('student.upload.allowed_mimes', ['pdf', 'jpg', 'jpeg', 'png'])),
                'max:' . config('student.upload.max_size_kb', 5120),
            ],
        ]);

        $existing = $student->documents()->where('document_type', $validated['document_type'])->first();

        if ($existing) {
            $existing->deleteFile();
            $existing->delete();
        }

        $file = $request->file('file');
        $path = $file->store("student-documents/{$student->id}", 'local');

        $student->documents()->create([
            'document_type' => $validated['document_type'],
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function download(StudentDocument $document)
    {
        $student = Auth::guard('student')->user();
        abort_unless($document->student_id === $student->id, 403);

        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function destroy(StudentDocument $document)
    {
        $student = Auth::guard('student')->user();
        abort_unless($document->student_id === $student->id, 403);

        if ($student->application_status === 'submitted') {
            return back()->with('error', 'Documents cannot be removed after submission.');
        }

        $document->deleteFile();
        $document->delete();

        return back()->with('success', 'Document removed.');
    }
}
