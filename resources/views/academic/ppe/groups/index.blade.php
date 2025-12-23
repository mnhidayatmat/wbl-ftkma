@extends('layouts.app')

@section('title', 'PPE Evaluation - Select Group')

@section('content')
<div class="py-6">
        <div class="max-w-7xl mx-auto px-10">
            <div class="mb-6">
                <p class="text-gray-600 dark:text-gray-400">
                    Select a group to view and manage student evaluations.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                @foreach($groups as $group)
                    @php
                        $user = auth()->user();
                        if ($user->isLecturer()) {
                            $route = route('academic.ppe.lecturer.index', ['group' => $group->id]);
                        } elseif ($user->isIndustry()) {
                            $route = route('academic.ppe.ic.index', ['group' => $group->id]);
                        } else {
                            $route = route('academic.ppe.final.index', ['group' => $group->id]);
                        }
                    @endphp
                    <a href="{{ $route }}" 
                       class="group block p-6 bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-200 border-2 border-transparent hover:border-[#0084C5]">
                        <div class="text-center">
                            <div class="mb-4">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#003A6C]/10 dark:bg-[#003A6C]/20 group-hover:bg-[#0084C5]/10 dark:group-hover:bg-[#0084C5]/20 transition-colors">
                                    <svg class="w-8 h-8 text-[#003A6C] dark:text-[#0084C5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-[#003A6C] dark:text-[#0084C5] mb-2">
                                {{ $group->name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $group->students_count }} students
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($groups->isEmpty())
                <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl shadow-md">
                    <p class="text-gray-600 dark:text-gray-400">No groups available.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

