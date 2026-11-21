@extends('layouts.app')

@section('content')
@php
    $counts = [
        'student' => \Illuminate\Support\Facades\DB::table('users')->where('role', 'student')->count(),
        'teacher' => \Illuminate\Support\Facades\DB::table('users')->where('role', 'teacher')->count(),
        'counselor' => \Illuminate\Support\Facades\DB::table('users')->where('role', 'counselor')->count(),
        'clinic' => \Illuminate\Support\Facades\DB::table('users')->where('role', 'clinic')->count(),
        'disciplinary' => \Illuminate\Support\Facades\DB::table('users')->where('role', 'disciplinary')->count(),
        'admin' => \Illuminate\Support\Facades\DB::table('users')->where('role', 'admin')->count(),
    ];
    $totalUsers = array_sum($counts);
    $totalCases = \Illuminate\Support\Facades\Schema::hasTable('cases')
        ? \Illuminate\Support\Facades\DB::table('cases')->count() : 0;
@endphp

<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: var(--gc-primary); text-transform: uppercase; letter-spacing: 0.05em;">Admin Portal</p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">User Management</h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">Manage system users and roles for GuidanceCare.</p>
                </div>
                <a href="{{ route('admin.users.create') }}"
                   style="display: inline-block; background: var(--gc-primary); color: white; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none;">
                    + Add User
                </a>
            </div>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #dcfce7; color: #15803d; border-radius: 8px; font-size: 14px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #fee2e2; color: #b91c1c; border-radius: 8px; font-size: 14px;">{{ session('error') }}</div>
        @endif

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 20px;">
            <a href="{{ route('admin.users.index') }}" class="gc-stat" aria-label="Total Users: open the list">
                <p class="gc-stat-label">Total Users</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; color: #111827;">{{ $totalUsers }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'student']) }}" class="gc-stat" aria-label="Students: open the list">
                <p class="gc-stat-label">Students</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; color: var(--gc-primary);">{{ $counts['student'] }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'teacher']) }}" class="gc-stat" aria-label="Teachers: open the list">
                <p class="gc-stat-label">Teachers</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; color: #111827;">{{ $counts['teacher'] }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'counselor']) }}" class="gc-stat" aria-label="Counselors: open the list">
                <p class="gc-stat-label">Counselors</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; color: #111827;">{{ $counts['counselor'] }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'clinic']) }}" class="gc-stat" aria-label="Clinic: open the list">
                <p class="gc-stat-label">Clinic</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; color: #111827;">{{ $counts['clinic'] }}</p>
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'disciplinary']) }}" class="gc-stat" aria-label="Disciplinary: open the list">
                <p class="gc-stat-label">Disciplinary</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; color: #111827;">{{ $counts['disciplinary'] }}</p>
            </a>
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px;">
                <p style="margin: 0 0 4px 0; font-size: 12px; font-weight: 500; color: #6b7280;">Cases</p>
                <p style="margin: 0; font-size: 28px; font-weight: 700; color: #111827;">{{ $totalCases }}</p>
            </div>
        </div>

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px;">
            <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600; color: #111827;">Quick Actions</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                <a href="{{ route('admin.users.index') }}"
                   style="display: inline-block; background: var(--gc-primary); color: white; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none;">
                    View All Users
                </a>
                <a href="{{ route('admin.users.create') }}"
                   style="display: inline-block; background: #f3f4f6; color: #374151; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; border: 1px solid #e5e7eb;">
                    Add New User
                </a>
            </div>
        </div>

    </div>
</div>
@endsection