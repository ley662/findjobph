<?php

namespace App\Http\Controllers;

use App\Models\ApplicantDocument;
use App\Models\JobApplication;
use App\Models\RequirementType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ApplicantDashboardController extends Controller
{
    /**
     * Display applicant's Requirements Vault.
     */
    public function documents(): View
    {
        $user = Auth::user();
        $user->load('documents.requirementType');

        $requirementTypes = RequirementType::all();
        $userDocuments = $user->documents->keyBy('requirement_type_id');

        $totalTypes = $requirementTypes->count();
        $uploadedCount = $userDocuments->count();
        $completionRate = $totalTypes > 0 ? (int) round(($uploadedCount / $totalTypes) * 100) : 0;

        return view('applicant.documents', compact(
            'requirementTypes',
            'userDocuments',
            'completionRate',
            'totalTypes',
            'uploadedCount'
        ));
    }

    /**
     * Upload or replace a document in the vault.
     */
    public function uploadDocument(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'requirement_type_id' => ['required', 'exists:requirement_types,id'],
            'document_file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $file = $request->file('document_file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType() ?? 'application/octet-stream';
        $fileSize = $file->getSize();

        // Check if existing document to replace
        $existing = ApplicantDocument::where('user_id', $user->id)
            ->where('requirement_type_id', $validated['requirement_type_id'])
            ->first();

        if ($existing && Storage::disk('public')->exists($existing->file_path)) {
            Storage::disk('public')->delete($existing->file_path);
        }

        $path = $file->store("documents/{$user->id}", 'public');

        ApplicantDocument::updateOrCreate(
            [
                'user_id' => $user->id,
                'requirement_type_id' => $validated['requirement_type_id'],
            ],
            [
                'file_path' => $path,
                'file_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $type = RequirementType::find($validated['requirement_type_id']);

        return back()->with('success', "{$type?->name} has been successfully uploaded to your Requirements Vault!");
    }

    /**
     * Delete a document from the vault.
     */
    public function deleteDocument(ApplicantDocument $document): RedirectResponse
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $documentName = $document->requirementType?->name ?? 'Document';
        $document->delete();

        return back()->with('success', "{$documentName} has been removed from your Requirements Vault.");
    }

    /**
     * Display applicant's submitted applications.
     */
    public function applications(): View
    {
        $user = Auth::user();

        $applications = JobApplication::where('applicant_id', $user->id)
            ->with(['jobPost.employer', 'jobPost.category', 'jobPost.requirements'])
            ->latest()
            ->paginate(10);

        return view('applicant.applications', compact('applications'));
    }
}
