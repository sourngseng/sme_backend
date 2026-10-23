@extends('layouts.dashboard')

@section('title', 'Platform Dashboard')
@section('header_title', 'User Portal')

@section('content')
    <div style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.5rem; font-weight: 800; color: #0f172a;">Welcome, {{ auth()->user()->full_name }}!</h2>
        <p style="font-size: 0.875rem; color: #64748b; margin-top: 0.25rem;">SchoolMeal platform access active.</p>
    </div>
@endsection
