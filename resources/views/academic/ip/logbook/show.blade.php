@extends('layouts.app')

@section('title', 'Integrated Project Logbook Evaluation - ' . $student->name)

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
    'moduleType' => 'IP',
    'backRoute' => 'academic.ip.ic.show',
    'storeRoute' => 'academic.ip.logbook.store'
])
@endsection
