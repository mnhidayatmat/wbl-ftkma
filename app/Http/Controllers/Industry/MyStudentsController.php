<?php

namespace App\Http\Controllers\Industry;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\WblGroup;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyStudentsController extends Controller
{
    /**
     * Display the list of students assigned to the industry coach.
     */
    public function index(Request $request): View
    {
        // Only industry coach can access
        if (! auth()->user()->isIndustry()) {
            abort(403, 'Unauthorized access.');
        }

        $icId = auth()->id();
        $query = Student::where('ic_id', $icId)
            ->with(['group', 'company', 'academicTutor']);

        // Filter by group
        if ($request->has('group') && $request->group) {
            $query->where('group_id', $request->group);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('matric_no', 'like', "%{$search}%");
            });
        }

        // Industry Coach can only see students in active groups
        $query->inActiveGroups();

        $students = $query->orderBy('name')->paginate(20)->withQueryString();

        // Get only active groups for filter
        $groups = WblGroup::where('status', 'ACTIVE')->orderBy('name')->get();

        return view('industry.students.index', compact('students', 'groups'));
    }
}
