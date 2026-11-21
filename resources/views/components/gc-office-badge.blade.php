@props(['office' => ''])

{{-- Which office a referral goes to. Outlined pill so it is never confused with a status badge. --}}
@php
    $kind = match ($office) {
        'Clinic'       => 'clinic',
        'Disciplinary' => 'disciplinary',
        default        => 'neutral',
    };
@endphp
<span {{ $attributes->merge(['class' => 'gc-badge gc-office gc-office-' . $kind]) }}>{{ $office ?: '—' }}</span>
