@php
use Illuminate\Support\Facades\Route;

$navLinks = [
[
'label' => 'Inicio',
'route' => 'home',
'fallback' => url('/')
],
[
'label' => 'Locales',
'route' => 'locales.index',
'fallback' => url('/locales')
],
[
'label' => 'Promociones',
'route' => 'promociones.index',
'fallback' => url('/promociones')
],
[
'label' => 'Quiénes Somos',
'route' => 'about',
'fallback' => url('/quienes-somos')
],
[
'label' => 'Contacto',
'route' => 'contact',
'fallback' => url('/contacto')
],
];

$brandUrl = Route::has('home') ? route('home') : url('/');
@endphp

@php
// Apply navbar-overlay class only on home page for transparent effect over hero carousel
$isHomePage = request()->routeIs('home') || request()->is('/');
$navbarClasses = $isHomePage ? 'navbar navbar-expand-lg navbar-light navbar-overlay' : 'navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm';
@endphp

<nav class="{{ $navbarClasses }}">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ $brandUrl }}">
      <img src="{{ asset('images/branding/logoBYG.png') }}" alt="Shopping Rosario" height="40" class="me-2">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-navbar" aria-controls="main-navbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="main-navbar">
      <ul class="navbar-nav ms-auto align-items-lg-center mb-2 mb-lg-0">
        @foreach ($navLinks as $link)
        @php
        $isActive = request()->routeIs($link['route'] ?? '') || request()->routeIs(($link['route'] ?? '') . '.*');
        $url = Route::has($link['route']) ? route($link['route']) : $link['fallback'];
        @endphp
        <li class="nav-item">
          <a class="nav-link {{ $isActive ? 'active' : '' }}" href="{{ $url }}">
            {{ $link['label'] }}
          </a>
        </li>
        @endforeach

        @guest
        @php
        $loginUrl = Route::has('login') ? route('login') : url('/login');
        $registerUrl = Route::has('register') ? route('register') : url('/register');
        @endphp
        <li class="nav-item ms-lg-2">
          <a class="btn btn-outline-primary btn-sm" href="{{ $loginUrl }}">
            <i class="bi bi-box-arrow-in-right"></i> Ingresar
          </a>
        </li>
        <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
          <a class="btn btn-primary btn-sm" href="{{ $registerUrl }}">
            <i class="bi bi-person-plus"></i> Registrarse
          </a>
        </li>
        @else
        <li class="nav-item dropdown ms-lg-3">
          <a class="nav-link dropdown-toggle" href="#" id="user-menu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="user-menu">
            @if(Auth::user()->tipo_usuario === 'administrador')
            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">
              <i class="bi bi-speedometer2"></i> Panel Administrador
            </a></li>
            @endif
            
            @if(Auth::user()->tipo_usuario === 'dueño de local')
            <li><a class="dropdown-item" href="{{ route('store.dashboard') }}">
              <i class="bi bi-shop"></i> Panel de Local
            </a></li>
            @endif
            
            @if(Auth::user()->tipo_usuario === 'cliente')
            <li><a class="dropdown-item" href="{{ route('client.dashboard') }}">
              <i class="bi bi-person-badge"></i> Mi Cuenta
            </a></li>
            @endif
            
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">
                  <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                </button>
              </form>
            </li>
          </ul>
        </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>