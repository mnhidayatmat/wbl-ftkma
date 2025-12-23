@extends('layouts.app')

@section('title', 'Occupational Safety & Health Logbook Evaluation - ' . $student->name)

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
    'moduleType' => 'OSH',
    'backRoute' => 'academic.osh.ic.show',
    'storeRoute' => 'academic.osh.logbook.store'
])
@endsection
