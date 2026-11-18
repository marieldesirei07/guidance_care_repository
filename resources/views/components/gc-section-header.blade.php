@props(['title', 'href' => null, 'label' => 'View All →'])

{{-- Standard table-card header: title on the left, "View All →" on the right. Use this in every portal. --}}
<div class="gc-card-header">
    <h2 class="gc-card-title">{{ $title }}</h2>
    @if($href)
        <a href="{{ $href }}" class="gc-link-all">{{ $label }}</a>
    @endif
</div>
