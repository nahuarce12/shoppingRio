<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@yield('title', 'Panel - ' . config('app.name', 'ShoppingRio'))</title>
  <meta name="description" content="@yield('meta_description', 'Gestioná tu actividad en Shopping Rosario')">

  <link rel="icon" type="image/png" href="{{ asset('images/branding/logoFavIconBYG.png') }}">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  @stack('styles')

  <!-- Vite: Bootstrap CSS + JS + Custom Assets -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @stack('head')
</head>

<body class="bg-light">
  @hasSection('dashboard_nav')
  @yield('dashboard_nav')
  @else
  <x-nav.main />
  @endif

  <main id="dashboard-content" class="py-4">
    {{-- Flash Messages --}}
    <div class="container mb-3">
      <x-flash-messages />
    </div>

    @yield('content')
  </main>

  <x-footer.main />

  {{-- Dashboard-specific scripts loaded via vite directive in individual dashboard views --}}
  @stack('scripts')
</body>

</html>