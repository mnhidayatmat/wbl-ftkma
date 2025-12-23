<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

class SearchController extends Controller
{
    /**
     * Global search functionality
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'results' => [],
                'message' => 'Please enter at least 2 characters'
            ]);
        }

        $results = [];

        // Search Students
        $students = Student::where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('matric_no', 'like', "%{$query}%");
            })
            ->with(['group', 'company'])
            ->limit(5)
            ->get()
            ->map(function ($student) {
                return [
                    'type' => 'student',
                    'id' => $student->id,
                    'title' => $student->name,
                    'subtitle' => $student->matric_no . ' • ' . ($student->group ? $student->group->name : 'No Group'),
                    'url' => route('students.show', $student->id),
                    'icon' => 'user'
                ];
            });

        // Search Companies
        $companies = Company::where(function($q) use ($query) {
                $q->where('company_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($company) {
                return [
                    'type' => 'company',
                    'id' => $company->id,
                    'title' => $company->company_name,
                    'subtitle' => $company->email ?? 'No email',
                    'url' => route('companies.show', $company->id),
                    'icon' => 'building'
                ];
            });

        // Search Users (if admin)
        $users = collect();
        if (auth()->user()->isAdmin()) {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'type' => 'user',
                        'id' => $user->id,
                        'title' => $user->name,
                        'subtitle' => $user->email . ' • ' . ucfirst($user->role),
                        'url' => '#', // Add user profile route if exists
                        'icon' => 'user-circle'
                    ];
                });
        }

        $results = $students->concat($companies)->concat($users)->take(10);

        return response()->json([
            'results' => $results->values(),
            'count' => $results->count()
        ]);
    }
}

