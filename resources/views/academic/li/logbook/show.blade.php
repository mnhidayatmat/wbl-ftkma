@extends('layouts.app')

@section('title', 'Industrial Training Logbook Evaluation - ' . $student->name)

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
    'moduleType' => 'LI',
    'backRoute' => 'academic.li.ic.show',
    'storeRoute' => 'academic.li.logbook.store'
])
@endsection
