<?php

namespace App\Http\Controllers;

use App\Models\ReferenceSample;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ReferenceSampleController extends Controller
{
    /**
     * Display a listing of reference samples for admin/coordinator.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Only admin and coordinator can manage samples
        if (! $user->isAdmin() && ! $user->hasRole('coordinator')) {
            abort(403, 'Unauthorized access');
        }

        $query = ReferenceSample::with('uploader');

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $samples = $query->ordered()->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => ReferenceSample::count(),
            'active' => ReferenceSample::active()->count(),
            'inactive' => ReferenceSample::where('is_active', false)->count(),
            'resume' => ReferenceSample::active()->byCategory('resume')->count(),
            'poster' => ReferenceSample::active()->byCategory('poster')->count(),
            'achievement' => ReferenceSample::active()->byCategory('achievement')->count(),
        ];

        return view('reference-samples.index', compact('samples', 'stats'));
    }

    /**
     * Show the form for creating a new reference sample.
     */
    public function create(): View
    {
        $user = Auth::user();

        if (! $user->isAdmin() && ! $user->hasRole('coordinator')) {
            abort(403, 'Unauthorized access');
        }

        return view('reference-samples.create');
    }

    /**
     * Store a newly created reference sample.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isAdmin() && ! $user->hasRole('coordinator')) {
            abort(403, 'Unauthorized access');
        }

        // Log incoming request for debugging
        \Log::info('Reference sample upload attempt', [
            'user_id' => $user->id,
            'has_file' => $request->hasFile('file'),
            'file_valid' => $request->hasFile('file') ? $request->file('file')->isValid() : false,
            'all_input' => $request->except('file'),
        ]);

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'category' => 'required|in:resume,poster,achievement,other',
                'description' => 'nullable|string|max:1000',
                'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png|max:10240', // 10MB max
                'is_active' => 'nullable|boolean',
                'display_order' => 'nullable|integer|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Reference sample validation failed', [
                'errors' => $e->errors(),
                'user_id' => $user->id,
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Validation failed. Please check the form and try again.');
        }

        try {
            $file = $request->file('file');

            if (!$file) {
                \Log::warning('No file in request after validation', ['user_id' => $user->id]);
                return back()
                    ->withInput()
                    ->with('error', 'No file was uploaded. Please select a file.');
            }

            \Log::info('Storing file', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ]);

            $path = $file->store('reference-samples');

            \Log::info('File stored successfully', ['path' => $path]);

            ReferenceSample::create([
                'title' => $validated['title'],
                'category' => $validated['category'],
                'description' => $validated['description'] ?? null,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => $user->id,
                'is_active' => $validated['is_active'] ?? true,
                'display_order' => $validated['display_order'] ?? 0,
            ]);

            \Log::info('Reference sample created successfully', ['path' => $path]);

            return redirect()
                ->route('reference-samples.index')
                ->with('success', 'Reference sample uploaded successfully.');
        } catch (\Exception $e) {
            \Log::error('Reference sample upload failed: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['file' => 'Failed to upload: ' . $e->getMessage()])
                ->with('error', 'Failed to upload reference sample: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified reference sample.
     */
    public function show(ReferenceSample $referenceSample): View
    {
        $referenceSample->load('uploader');

        return view('reference-samples.show', compact('referenceSample'));
    }

    /**
     * Show the form for editing the specified reference sample.
     */
    public function edit(ReferenceSample $referenceSample): View
    {
        $user = Auth::user();

        if (! $user->isAdmin() && ! $user->hasRole('coordinator')) {
            abort(403, 'Unauthorized access');
        }

        return view('reference-samples.edit', compact('referenceSample'));
    }

    /**
     * Update the specified reference sample.
     */
    public function update(Request $request, ReferenceSample $referenceSample): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isAdmin() && ! $user->hasRole('coordinator')) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:resume,poster,achievement,other',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png|max:10240',
            'is_active' => 'nullable|boolean',
            'display_order' => 'nullable|integer|min:0',
        ]);

        try {
            $updateData = [
                'title' => $validated['title'],
                'category' => $validated['category'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? $referenceSample->is_active,
                'display_order' => $validated['display_order'] ?? $referenceSample->display_order,
            ];

            // Handle file replacement
            if ($request->hasFile('file')) {
                // Delete old file
                if (Storage::exists($referenceSample->file_path)) {
                    Storage::delete($referenceSample->file_path);
                }

                $file = $request->file('file');
                $path = $file->store('reference-samples');

                $updateData['file_path'] = $path;
                $updateData['file_name'] = $file->getClientOriginalName();
                $updateData['file_size'] = $file->getSize();
                $updateData['mime_type'] = $file->getMimeType();
            }

            $referenceSample->update($updateData);

            return redirect()
                ->route('reference-samples.index')
                ->with('success', 'Reference sample updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Reference sample update failed: ' . $e->getMessage(), [
                'sample_id' => $referenceSample->id,
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['file' => 'Failed to update: ' . $e->getMessage()])
                ->with('error', 'Failed to update reference sample: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified reference sample.
     */
    public function destroy(ReferenceSample $referenceSample): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isAdmin() && ! $user->hasRole('coordinator')) {
            abort(403, 'Unauthorized access');
        }

        try {
            // Delete file from storage
            if (Storage::exists($referenceSample->file_path)) {
                Storage::delete($referenceSample->file_path);
            }

            $referenceSample->delete();

            return redirect()
                ->route('reference-samples.index')
                ->with('success', 'Reference sample deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete reference sample.');
        }
    }

    /**
     * Download a reference sample.
     */
    public function download(ReferenceSample $referenceSample)
    {
        // Only active samples can be downloaded by students
        if (! $referenceSample->is_active && ! Auth::user()->isAdmin() && ! Auth::user()->hasRole('coordinator')) {
            abort(403, 'This reference sample is not available for download.');
        }

        if (! Storage::exists($referenceSample->file_path)) {
            abort(404, 'File not found');
        }

        // Increment download count
        $referenceSample->incrementDownloadCount();

        return Storage::download($referenceSample->file_path, $referenceSample->file_name);
    }

    /**
     * Toggle active status of a reference sample.
     */
    public function toggleStatus(ReferenceSample $referenceSample): RedirectResponse
    {
        $user = Auth::user();

        if (! $user->isAdmin() && ! $user->hasRole('coordinator')) {
            abort(403, 'Unauthorized access');
        }

        $referenceSample->update([
            'is_active' => ! $referenceSample->is_active,
        ]);

        $status = $referenceSample->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Reference sample has been {$status}.");
    }

    /**
     * Display reference samples for students.
     */
    public function studentIndex(): View
    {
        $samples = ReferenceSample::active()
            ->ordered()
            ->get()
            ->groupBy('category');

        return view('reference-samples.student-index', compact('samples'));
    }
}
