<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light">

    <title>GuidanceCare — Log in</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @include('layouts.gc-ui')
    <style>
        /* Branded sign-in: STI blue panel on the left, form on the right; stacks on phones */
        .gc-auth { min-height: 100vh; display: grid; grid-template-columns: 1fr; background: #f5f7fb; }
        .gc-auth-brand { background: var(--gc-primary); color: #fff; padding: 28px 24px; display: flex; flex-direction: column; gap: 20px; }
        .gc-auth-school { display: flex; align-items: center; gap: 12px; font-size: 13px; line-height: 1.35; color: #dce5f7; }
        .gc-auth-school img { height: 44px; width: auto; background: #fff; border-radius: 8px; padding: 4px; }
        .gc-auth-mark { width: 44px; height: 44px; flex: none; border-radius: 10px; background: var(--gc-accent); color: var(--gc-primary); display: grid; place-items: center; font-weight: 700; }
        .gc-auth-brand h1 { margin: 0 0 8px; font-size: 32px; line-height: 1.1; font-weight: 700; color: #fff; }
        .gc-auth-brand p { margin: 0; max-width: 36ch; color: #dce5f7; font-size: 15px; line-height: 1.55; }
        .gc-auth-note { display: none; align-items: flex-start; gap: 8px; font-size: 13px; color: #c9d6f2; }
        .gc-auth-note svg { width: 18px; height: 18px; flex: none; margin-top: 1px; }
        .gc-auth-panel { display: flex; align-items: center; justify-content: center; padding: 32px 20px 40px; }
        .gc-auth-card { width: 100%; max-width: 400px; }

        .gc-auth-card h2 { margin: 0 0 4px; font-size: 26px; font-weight: 700; color: #111827; }
        .gc-auth-card .gc-subtitle { margin-bottom: 24px; }
        .gc-field { margin-bottom: 16px; }
        .gc-field label { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 500; color: #374151; }
        .gc-input { width: 100%; box-sizing: border-box; min-height: 46px; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; background: #fff; color: #111827; font-size: 16px; font-family: inherit; }
        .gc-input:focus { outline: none; border-color: var(--gc-primary); box-shadow: 0 0 0 3px rgba(0,56,168,.15); }
        .gc-input.is-invalid { border-color: var(--gc-danger); }
        .gc-error { margin: 6px 0 0; font-size: 13px; color: var(--gc-danger); }
        .gc-auth-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin: 4px 0 20px; font-size: 14px; }
        .gc-check { display: inline-flex; align-items: center; gap: 8px; color: #4b5563; cursor: pointer; }
        .gc-check input { width: 18px; height: 18px; accent-color: var(--gc-primary); }
        .gc-auth-row a { color: var(--gc-primary); font-weight: 500; text-decoration: none; }
        .gc-auth-row a:hover { text-decoration: underline; }
        .gc-btn-block { display: block; width: 100%; min-height: 48px; font-size: 15px; }
        .gc-auth-foot { margin-top: 28px; text-align: center; font-size: 12px; color: #9ca3af; }

        @media (min-width: 900px) {
            .gc-auth { grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
            .gc-auth-brand { padding: 48px clamp(32px, 5vw, 72px); justify-content: space-between; }
            .gc-auth-brand h1 { font-size: 48px; }
            .gc-auth-note { display: flex; }
            .gc-auth-panel { padding: 48px 32px; }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="gc-auth">
        <section class="gc-auth-brand">
            <div class="gc-auth-school">
                {{-- Put the official logo (with permission) at public/images/sti-logo.png and it appears here --}}
                @if (file_exists(public_path('images/sti-logo.png')))
                    <img src="{{ asset('images/sti-logo.png') }}" alt="STI College Global City">
                @else
                    <span class="gc-auth-mark" aria-hidden="true">GC</span>
                @endif
                <span>STI College Global City<br>Guidance Office</span>
            </div>

            <div>
                <h1>GuidanceCare</h1>
                <p>Centralized student guidance, counseling, case monitoring and referrals.</p>
            </div>

            <div class="gc-auth-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 11h12v10H6zM8 11V7a4 4 0 0 1 8 0v4"/></svg>
                <span>Confidential records are shared on a need-to-know basis.</span>
            </div>
        </section>

        <main class="gc-auth-panel">
            <div class="gc-auth-card">
                {{ $slot }}
                <p class="gc-auth-foot">GuidanceCare System • STI College Global City</p>
            </div>
        </main>
    </div>
</body>
</html>
