@extends('layouts.app')

@section('title', 'Professional Practice & Ethics Logbook Evaluation - ' . $student->name)

@section('content')
@include('components.logbook-evaluation', [
    'student' => $student,
    'periods' => $periods,
    'totalScore' => $totalScore,
    'completedPeriods' => $completedPeriods,
    'totalPeriods' => $totalPeriods,
    'maxPossibleScore' => $maxPossibleScore,
    'assessmentWeight' => $assessmentWeight,
    'canEdit' => $canEdit,
    'moduleType' => 'PPE',
    'backRoute' => 'academic.ppe.ic.show',
    'storeRoute' => 'academic.ppe.logbook.store'
])
@endsection
