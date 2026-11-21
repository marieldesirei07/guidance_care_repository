@extends('layouts.app')

@section('content')
<div style="padding-top: 32px; padding-bottom: 40px;">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px;">
            <div style="margin-bottom: 24px;">
                <p style="margin: 0 0 6px 0; font-size: 11px; font-weight: 600; color: var(--gc-primary); text-transform: uppercase; letter-spacing: 0.05em;">Admin Portal</p>
                <h1 style="margin: 0 0 6px 0; font-size: 22px; font-weight: 700; color: #111827;">Edit User</h1>
                <p style="margin: 0; font-size: 14px; color: #6b7280;">Update account details for {{ $user->name }}.</p>
            </div>

            @if ($errors->any())
                <div style="margin-bottom: 16px; padding: 14px 16px; background: #fee2e2; color: #b91c1c; border-radius: 8px; font-size: 14px;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">New Password (leave blank to keep)</label>
                    <input type="password" name="password" minlength="8"
                           style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    <p style="margin: 6px 0 0; font-size: 12px; color: #6b7280;">Min. 8 characters with an uppercase letter, a number and a symbol.</p>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Role</label>
                    <select name="role" required
                            style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; background: white; box-sizing: border-box;">
                        @foreach(['student','teacher','counselor','clinic','disciplinary','admin'] as $r)
                            <option value="{{ $r }}" {{ old('role', $user->role) === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Student Number</label>
                        <input type="text" name="student_number" value="{{ old('student_number', $user->student_number ?? '') }}"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px;">Employee ID</label>
                        <input type="text" name="employee_id" value="{{ old('employee_id', $user->employee_id ?? '') }}"
                               style="width: 100%; padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    </div>
                </div>
                <div style="display: flex; gap: 12px;">
                    <button type="submit"
                            style="background: var(--gc-primary); color: white; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; border: none; cursor: pointer;">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       style="display: inline-block; background: #f3f4f6; color: #374151; padding: 10px 18px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; border: 1px solid #e5e7eb;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection