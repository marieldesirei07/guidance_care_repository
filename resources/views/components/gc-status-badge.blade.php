@props(['status' => 'Pending', 'label' => null])

{{--
    The ONE status badge for every portal (student, teacher, counselor, clinic, disciplinary, admin).
    Same pill shape everywhere; colours come from the .gc-badge-* rules in layouts/gc-ui.blade.php.
--}}
@php
    $kind = match ($status) {
        'Pending', 'Requested'                                   => 'pending',   // amber
        'Ongoing', 'Scheduled', 'Accepted', 'In Progress',
        'Received', 'Rescheduled'                                => 'active',    // blue
        'For Follow-up'                                          => 'followup',  // yellow
        'Referred'                                               => 'referred',  // purple
        'Completed', 'Closed', 'Resolved'                        => 'done',      // green
        'Declined', 'Cancelled'                                  => 'danger',    // red
        default                                                  => 'neutral',   // gray
    };
@endphp
<span {{ $attributes->merge(['class' => 'gc-badge gc-badge-' . $kind]) }}>{{ $label ?? $status }}</span>
