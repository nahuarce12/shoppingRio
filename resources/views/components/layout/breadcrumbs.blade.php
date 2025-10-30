@props(['items' => []])

@php
use Illuminate\Support\Facades\Route;

$homeUrl = Route::has('home.index') ? route('home.index') : url('/');
@endphp

<nav aria-label="breadcrumb" class="bg-light border-bottom py-2">
  <div class="container">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ $homeUrl }}">Inicio</a></li>
      @foreach($items as $item)
      @if(isset($item['url']) && $item['url'])
      <li class="breadcrumb-item"><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
      @else
      <li class="breadcrumb-item active" aria-current="page">{{ $item['label'] }}</li>
      @endif
      @endforeach
    </ol>
  </div>
</nav>