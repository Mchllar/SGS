<?php

namespace App\Http\Controllers; 

use Illuminate\Support\Facades\DB;
use App\Models\Journal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JournalSubmissionController extends Controller
{
    public function journalSubmission()
    {
        return view('student.journal_submission');
    }
    
    public function store(Request $request)
    {
        // Form Validation
        $request->validate([
            'journal_title' => 'required', 
            'title_of_paper' => 'required',
            'status' => 'required',
            'file_upload' => 'required', 
        ]);
    
        // Ensure the directory exists
        Storage::makeDirectory('public/files');

        // Handle File Upload
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/files', $fileName);

            // Get the authenticated student's student_number
            $user_id = Auth::user()->id;

            
            // Create New Journal Entry
            $journal = new Journal();
            $journal->user_id = $user_id; // Use student_id as foreign key reference
            $journal->journal_title = $request->journal_title;
            $journal->title_of_paper = $request->title_of_paper;
            $journal->status = $request->status;
            $journal->file_upload = $fileName; // Store the file path in the database
            $journal->save();

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Journal Publication submitted successfully.');
        } else {
            return redirect()->back()->with('error', 'File upload failed.'); // Handle file upload failure
        }
    }
        // Retrieving Records
        public function index()
        {
            // Retrieve the currently authenticated user
            $user = auth()->user();
           
            // Retrieve journals submitted by the authenticated user
            $journals = Journal::where('user_id', auth()->id())->get();
            
            return view('student.journal_records', compact('journals'));
        }
}
