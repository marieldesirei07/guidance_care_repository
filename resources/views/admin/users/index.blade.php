@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em;">Admin Portal</p>
                    <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">All Users</h1>
                    <p style="margin: 0; font-size: 14px; color: #6b7280;">View, edit, or delete system accounts.</p>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="{{ route('admin.dashboard') }}"
                       style="display: inline-block; font-size: 13px; color: #374151; text-decoration: none; font-weight: 500;
                              background: #f3f4f6; padding: 10px 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
                        ← Back to Dashboard
                    </a>
                    <a href="{{ route('admin.users.create') }}"
                       style="display: inline-block; background: #2563eb; color: white; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none;">
                        + Add User
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #dcfce7; color: #15803d; border-radius: 8px; font-size: 14px;">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div style="margin-bottom: 16px; padding: 14px 16px; background: #fee2e2; color: #b91c1c; border-radius: 8px; font-size: 14px;">{{ session('error') }}</div>
        @endif

        {{-- Role filters --}}
        <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap;">
            @php
                $roles = ['' => 'All', 'student' => 'Student', 'teacher' => 'Teacher', 'counselor' => 'Counselor', 'clinic' => 'Clinic', 'disciplinary' => 'Disciplinary', 'admin' => 'Admin'];
            @endphp
            @foreach($roles as $key => $label)
                <a href="{{ route('admin.users.index', $key ? ['role' => $key] : []) }}"
                   style="display: inline-block; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none;
                          border: 1px solid {{ ($role ?? '') === $key ? '#2563eb' : '#e5e7eb' }};
                          background: {{ ($role ?? '') === $key ? '#2563eb' : '#ffffff' }};
                          color: {{ ($role ?? '') === $key ? '#ffffff' : '#374151' }};">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden;">
            @if($users->isEmpty())
                <p style="padding: 24px; margin: 0; font-size: 14px; color: #9ca3af;">No users found.</p>
            @else
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <thead>
                            <tr style="background: #f9fafb; text-align: left;">
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Name</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Email</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">Role</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase;">ID / Number</th>
                                <th style="padding: 12px 24px; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $u)
                                <tr style="border-top: 1px solid #f3f4f6;">
                                    <td style="padding: 14px 24px; color: #111827; font-weight: 500;">{{ $u->name }}</td>
                                    <td style="padding: 14px 24px; color: #374151;">{{ $u->email }}</td>
                                    <td style="padding: 14px 24px;">
                                        <span style="display:inline-block; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background:#dbeafe; color:#1d4ed8;">
                                            {{ ucfirst($u->role) }}
                                        </span>
                                        @unless($u->is_active ?? true)
                                            <span style="display:inline-block; margin-left:6px; padding:4px 10px; border-radius:9999px; font-size:12px; font-weight:500; background:#f3f4f6; color:#6b7280;">Inactive</span>
                                        @endunless
                                    </td>
                                    <td style="padding: 14px 24px; color: #6b7280; font-size: 13px;">
                                        {{ $u->student_number ?? $u->employee_id ?? '—' }}
                                    </td>
                                    <td style="padding: 14px 24px; text-align: right; white-space: nowrap;">
                                        <a href="{{ route('admin.users.edit', $u->id) }}"
                                           style="display: inline-block; background: #2563eb; color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 500; margin-right: 8px;">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" style="display: inline;"
                                              onsubmit="return confirm('{{ ($u->is_active ?? true) ? 'Deactivate this account? They will not be able to sign in, but their records are kept.' : 'Reactivate this account?' }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="{{ ($u->is_active ?? true) ? 'Deactivate' : 'Reactivate' }}"
                                                    style="display: inline-block; padding: 6px 12px; border: none; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; vertical-align: middle; {{ ($u->is_active ?? true) ? 'background:#fef3c7; color:#b45309;' : 'background:#dcfce7; color:#15803d;' }}">
                                                {{ ($u->is_active ?? true) ? 'Deactivate' : 'Reactivate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div style="margin-top: 48px; padding: 24px 0; text-align: center;">
            <p style="font-size: 12px; color: #9ca3af; margin: 0;">GuidanceCare System • STI College Global City • Centralized Student Support & Privacy Compliant</p>
        </div>
    </div>
</div>
@endsection