@props(['status' => 'Pending', 'label' => null])

{{-- One status → colour mapping for every table in every portal. --}}
@php
    $kind = match ($status) {
        'Pending', 'Requested'                                  => 'pending',
        'Scheduled', 'Accepted', 'Ongoing', 'Received',
        'In Progress', 'Rescheduled'                            => 'active',
        'For Follow-up'                                         => 'followup',
        'Completed', 'Resolved'                                 => 'done',
        'Cancelled', 'Declined'                                 => 'danger',
        default                                                 => 'neutral',
    };
@endphp
<span class="gc-badge gc-badge-{{ $kind }}">{{ $label ?? $status }}</span>
