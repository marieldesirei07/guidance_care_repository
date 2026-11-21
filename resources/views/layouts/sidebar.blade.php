{{--
    Role sidebar (replaces the old Breeze top bar).
    - Desktop (900px and wider): fixed blue sidebar on the left.
    - Phones and small tablets: a blue top bar with a menu button that opens the same links.
    Links are chosen by the signed-in user's role, so each portal only shows its own pages
    (ISO 9241-110: suitability for the task). Counts are one small query per page load.
--}}
@php
    $gcUser = auth()->user();
    $gcRole = $gcUser->role ?? '';

    // Simple outline icons (stroke paths, 24x24)
    $gcIcons = [
        'home'     => 'M3 11l9-7 9 7v9a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z',
        'inbox'    => 'M4 4h16v11h-5l-1.5 3h-3L9 15H4z',
        'folder'   => 'M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z',
        'calendar' => 'M4 6h16v14H4zM4 10h16M8 3v4M16 3v4',
        'swap'     => 'M7 7h13l-3-3M17 17H4l3 3',
        'doc'      => 'M6 3h8l4 4v14H6zM14 3v4h4M9 12h6M9 16h6',
        'plus'     => 'M12 5v14M5 12h14',
        'users'    => 'M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM2 21a7 7 0 0 1 14 0M17 11a3 3 0 1 0 0-6M22 21a6 6 0 0 0-4-5.6',
        'heart'    => 'M12 20s-7-4.5-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.5-7 10-7 10zM9 11h6M12 8v6',
        'flag'     => 'M5 21V4M5 4h11l-2 4 2 4H5',
        'key'      => 'M15 7a4 4 0 1 1-3.9 5H3v3h3v3h3v-3h2.1A4 4 0 0 1 15 7z',
        'logout'   => 'M15 4h4v16h-4M10 8l-4 4 4 4M6 12h10',
        'menu'     => 'M4 6h16M4 12h16M4 18h16',
        'close'    => 'M6 6l12 12M18 6L6 18',
    ];

    // Pending counts (one round trip; only for roles that show a badge)
    $gcCounts = ['pending' => 0, 'office' => 0];
    try {
        if ($gcRole === 'counselor') {
            $row = \Illuminate\Support\Facades\DB::selectOne(
                "select (select count(*) from referrals where status = 'Pending')
                      + (select count(*) from counseling_requests where status = 'Pending')
                      + (select count(*) from appointment_requests where status = 'Pending') as pending"
            );
            $gcCounts['pending'] = (int) ($row->pending ?? 0);
        } elseif (in_array($gcRole, ['clinic', 'disciplinary'])) {
            $gcCounts['office'] = \Illuminate\Support\Facades\DB::table('office_referrals')
                ->where('office', $gcRole === 'clinic' ? 'Clinic' : 'Disciplinary')
                ->whereIn('status', ['Pending', 'Received', 'In Progress'])
                ->count();
        }
    } catch (\Throwable $e) {
        // A count must never break navigation
    }

    // [label, url, icon, active patterns, badge count]
    $gcMenus = [
        'student' => [
            ['Dashboard', '/student/dashboard', 'home', ['student/dashboard']],
            ['New request', '/student/request', 'plus', ['student/request']],
            ['My requests', '/student/requests', 'inbox', ['student/requests', 'student/requests/*']],
            ['My appointments', '/student/appointments', 'calendar', ['student/appointments', 'student/appointments/*']],
            ['Teacher referrals', '/student/teacher-referrals', 'doc', ['student/teacher-referrals*']],
        ],
        'teacher' => [
            ['Dashboard', '/teacher/dashboard', 'home', ['teacher/dashboard']],
            ['Refer a student', '/teacher/referrals/create', 'plus', ['teacher/referrals/create']],
            ['My referrals', '/teacher/referrals', 'inbox', ['teacher/referrals', 'teacher/referrals/*']],
        ],
        'counselor' => [
            ['Dashboard', '/counselor/dashboard', 'home', ['counselor/dashboard']],
            ['Pending queue', '/counselor/pending', 'inbox', ['counselor/pending*'], $gcCounts['pending']],
            ['Cases', '/counselor/cases', 'folder', ['counselor/cases*']],
            ['Appointments', '/counselor/appointments', 'calendar', ['counselor/appointments*']],
            ['Office referrals', '/counselor/office-referrals', 'swap', ['counselor/office-referrals*', 'counselor/office-referral/*']],
            ['Teacher referrals', '/counselor/referrals', 'doc', ['counselor/referrals*']],
            ['Student requests', '/counselor/requests', 'doc', ['counselor/requests*']],
        ],
        'clinic' => [
            ['Dashboard', '/clinic/dashboard', 'home', ['clinic/dashboard']],
            ['Clinic referrals', '/clinic/referrals', 'heart', ['clinic/referrals*'], $gcCounts['office']],
        ],
        'disciplinary' => [
            ['Dashboard', '/disciplinary/dashboard', 'home', ['disciplinary/dashboard']],
            ['Behavioral referrals', '/disciplinary/referrals', 'flag', ['disciplinary/referrals*'], $gcCounts['office']],
        ],
        'admin' => [
            ['Dashboard', '/admin/dashboard', 'home', ['admin/dashboard']],
            ['Users', '/admin/users', 'users', ['admin/users', 'admin/users/*/edit']],
            ['Add user', '/admin/users/create', 'plus', ['admin/users/create']],
        ],
    ];
    $gcItems  = $gcMenus[$gcRole] ?? [['Dashboard', '/dashboard', 'home', ['dashboard']]];
    $gcPortal = ['student' => 'Student portal', 'teacher' => 'Teacher portal', 'counselor' => 'Counselor portal',
                 'clinic' => 'Clinic portal', 'disciplinary' => 'Disciplinary portal', 'admin' => 'Admin portal'][$gcRole] ?? 'GuidanceCare';
    $gcInitials = collect(explode(' ', trim($gcUser->name ?? '')))->filter()->take(2)
        ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');

    $gcIcon = fn ($name) => '<svg class="gc-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="' . $gcIcons[$name] . '"/></svg>';
@endphp

@php
    $gcExact = collect($gcItems)->first(fn ($i) => trim($i[1], '/') === request()->path())[1] ?? null;
@endphp
{{-- One set of links, used by both the sidebar and the phone menu --}}
@php ob_start(); @endphp
    <nav class="gc-nav" aria-label="Main">
        @foreach ($gcItems as $item)
            @php
                [$label, $url, $icon, $patterns] = $item;
                $count  = $item[4] ?? 0;
                // An exact URL match wins, so "Refer a student" and "My referrals" are never both highlighted
                $active = $gcExact ? $gcExact === $url : request()->is(...$patterns);
            @endphp
            <a href="{{ url($url) }}" class="gc-nav-link {{ $active ? 'is-active' : '' }}" @if ($active) aria-current="page" @endif>
                {!! $gcIcon($icon) !!}
                <span class="gc-nav-label">{{ $label }}</span>
                @if ($count > 0)
                    <span class="gc-nav-count" aria-label="{{ $count }} pending">{{ $count > 99 ? '99+' : $count }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="gc-side-user">
        <div class="gc-side-who">
            <span class="gc-avatar" aria-hidden="true">{{ $gcInitials ?: 'U' }}</span>
            <span class="gc-side-name">
                <strong>{{ $gcUser->name }}</strong>
                <small>{{ ucfirst($gcRole) }}</small>
            </span>
        </div>
        <a href="{{ route('password.change') }}" class="gc-nav-link gc-nav-link-sm {{ request()->is('password/change') ? 'is-active' : '' }}">
            {!! $gcIcon('key') !!}<span class="gc-nav-label">Change password</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="gc-nav-link gc-nav-link-sm gc-nav-button">
                {!! $gcIcon('logout') !!}<span class="gc-nav-label">Log out</span>
            </button>
        </form>
    </div>
@php $gcNavHtml = ob_get_clean(); @endphp

{{-- Desktop sidebar --}}
<aside class="gc-sidebar" aria-label="{{ $gcPortal }} navigation">
    <a href="{{ url($gcItems[0][1]) }}" class="gc-brand">
        <img src="{{ asset('images/guidancecare-logo.svg') }}" alt="" class="gc-brand-logo" width="36" height="36">
        <span class="gc-brand-text"><strong>GuidanceCare</strong><small>{{ $gcPortal }}</small></span>
    </a>
    {!! $gcNavHtml !!}
</aside>

{{-- Phone / small-tablet top bar + menu --}}
<header class="gc-topbar">
    <a href="{{ url($gcItems[0][1]) }}" class="gc-brand">
        <img src="{{ asset('images/guidancecare-logo.svg') }}" alt="" class="gc-brand-logo" width="36" height="36">
        <span class="gc-brand-text"><strong>GuidanceCare</strong><small>{{ $gcPortal }}</small></span>
    </a>
    <button type="button" class="gc-menu-btn" aria-expanded="false" aria-controls="gc-mobile-menu" aria-label="Open menu"
            onclick="var m=document.getElementById('gc-mobile-menu'),o=m.classList.toggle('is-open');this.setAttribute('aria-expanded',o);this.setAttribute('aria-label',o?'Close menu':'Open menu');this.querySelector('.i-open').style.display=o?'none':'';this.querySelector('.i-close').style.display=o?'':'none';">
        <svg class="i-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="{{ $gcIcons['menu'] }}"/></svg>
        <svg class="i-close" style="display:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="{{ $gcIcons['close'] }}"/></svg>
    </button>
</header>
<div id="gc-mobile-menu" class="gc-mobile-menu">
    {!! $gcNavHtml !!}
</div>
