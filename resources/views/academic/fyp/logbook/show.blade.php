@extends('layouts.app')

@section('title', 'FYP Logbook Evaluation - ' . $student->name)

@section('content')
@include('components.logbook-evaluation', [
    'student' => $student,
    'months' => $months,
    'totalScore' => $totalScore,
    'completedMonths' => $completedMonths,
    'assessmentWeight' => $assessmentWeight,
    'canEdit' => $canEdit,
    'moduleType' => 'FYP',
    'backRoute' => 'academic.fyp.ic.show',
    'storeRoute' => 'academic.fyp.logbook.store'
])
@endsection
