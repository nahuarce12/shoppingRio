<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'ShoppingRio') }}</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="d-flex flex-column min-vh-100">
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="{{ url('/') }}">
        {{ config('app.name', 'ShoppingRio') }}
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="#">Promociones</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Tiendas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Novedades</a>
          </li>
        </ul>

        <ul class="navbar-nav">
          {{-- Temporalmente comentado hasta implementar autenticación --}}
          {{--
                    @guest
                    --}}
          <li class="nav-item">
            <a class="nav-link" href="#">Iniciar Sesión</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Registrarse</a>
          </li>
          {{--
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ Auth::user()->name }}
          </a>
          <ul class="dropdown-menu">
            @if(Auth::user()->isAdmin())
            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Panel Admin</a></li>
            @endif
            @if(Auth::user()->isStoreOwner())
            <li><a class="dropdown-item" href="{{ route('store.dashboard') }}">Mi Tienda</a></li>
            @endif
            @if(Auth::user()->isClient())
            <li><a class="dropdown-item" href="{{ route('client.dashboard') }}">Mi Cuenta</a></li>
            @endif
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">Cerrar Sesión</button>
              </form>
            </li>
          </ul>
          </li>
          @endguest
          --}}
        </ul>
      </div>
    </div>
  </nav>

  <main class="flex-grow-1">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @yield('content')
  </main>

  <footer class="footer mt-auto py-3">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
        </div>
        <div class="col-md-6 text-end">
          <a href="#" class="text-light text-decoration-none">Contacto</a>
        </div>
      </div>
    </div>
  </footer>
</body>

</html>