<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'ShoppingRio') }} - Autenticación</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
  <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100">
      <div class="col-md-6 offset-md-3 col-lg-4 offset-lg-4">
        <div class="card shadow-sm">
          <div class="card-header text-center bg-primary text-white">
            <h4 class="mb-0">{{ config('app.name', 'ShoppingRio') }}</h4>
          </div>
          <div class="card-body">
            {{-- Flash Messages --}}
            <x-flash-messages />

            @yield('content')
          </div>
          <div class="card-footer text-center">
            <small class="text-muted">
              <a href="{{ url('/') }}" class="text-decoration-none">Volver al inicio</a>
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>