@props(['category'])

@php
$badgeClass = match($category) {
'initial' => 'bg-secondary',
'medium' => 'bg-warning text-dark',
'premium' => 'bg-success',
default => 'bg-secondary'
};

$categoryText = match($category) {
'initial' => 'Inicial',
'medium' => 'Medio',
'premium' => 'Premium',
default => ucfirst($category)
};
@endphp

<span class="badge {{ $badgeClass }} category-badge">
  {{ $categoryText }}
</span>