@extends('layouts.app')

@section('content')
<div class="gc-page">
    <div class="gc-container" style="max-width: 560px;">
        <div class="gc-card">
            <div class="gc-card-body">
                <p class="gc-eyebrow">Account Security</p>
                <h1 class="gc-title">Change Password</h1>
                <p class="gc-subtitle">
                    Use at least 8 characters with an uppercase letter, a lowercase letter, a number and a symbol.
                </p>

                @if (session('warning'))
                    <div class="gc-flash gc-flash-error" style="margin-top:16px;">{{ session('warning') }}</div>
                @endif
                @if (session('status') === 'password-updated')
                    <div class="gc-flash gc-flash-success" style="margin-top:16px;">Password updated.</div>
                @endif
                @if ($errors->updatePassword->any())
                    <div class="gc-flash gc-flash-error" style="margin-top:16px;">
                        <ul style="margin:0; padding-left:18px;">
                            @foreach ($errors->updatePassword->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" style="margin-top:20px;"
                      onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                    @csrf
                    @method('PUT')

                    @php $field = 'width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; box-sizing:border-box;'; @endphp

                    <div style="margin-bottom:16px;">
                        <label for="current_password" style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
                            {{ auth()->user()->must_change_password ? 'Temporary Password' : 'Current Password' }}
                        </label>
                        <input id="current_password" name="current_password" type="password" required autocomplete="current-password" style="{{ $field }}">
                    </div>

                    <div style="margin-bottom:16px;">
                        <label for="password" style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">New Password</label>
                        <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" style="{{ $field }}">
                    </div>

                    <div style="margin-bottom:20px;">
                        <label for="password_confirmation" style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Confirm New Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" style="{{ $field }}">
                    </div>

                    <div class="gc-actions">
                        <button type="submit" class="gc-btn gc-btn-primary">Save New Password</button>
                        @unless (auth()->user()->must_change_password)
                            <a href="{{ route('dashboard') }}" class="gc-btn gc-btn-secondary">Cancel</a>
                        @endunless
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
