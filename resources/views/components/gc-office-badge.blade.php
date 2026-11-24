@props(['office' => ''])

{{-- Which office a referral goes to. Same pill shape as the status badge, with its own colours. --}}
@php
    $kind = match ($office) {
        'Guidance'     => 'guidance',
        'Clinic'       => 'clinic',
        'Disciplinary' => 'disciplinary',
        default        => 'neutral',
    };
@endphp
<span {{ $attributes->merge(['class' => 'gc-badge gc-office gc-office-' . $kind]) }}>{{ $office ?: '—' }}</span>
