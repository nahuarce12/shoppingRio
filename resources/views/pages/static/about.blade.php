@extends('layouts.app')

@section('title', 'Quiénes Somos - Shopping Rosario')
@section('meta_description', 'Conocé la historia, valores y servicios que hacen único al Shopping Rosario.')

@php
use Illuminate\Support\Facades\Route;

$registerUrl = Route::has('auth.register') ? route('auth.register') : (Route::has('register') ? route('register') : '#');
@endphp

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Quiénes Somos']]" />

<section class="py-5">
  <div class="container text-center">
    <h1 class="display-4 fw-bold" style="color: var(--primary-color)">Shopping Rosario</h1>
    <p class="lead">El centro comercial más moderno de la ciudad</p>
  </div>
</section>

<section class="about-section py-4">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4">
        <h2 class="section-title">Nuestra Historia</h2>
        <p class="lead">Desde 2010, Shopping Rosario se ha convertido en el punto de encuentro favorito de las familias rosarinas.</p>
        <p>Ubicado en el corazón de Rosario, nuestro shopping cuenta con más de 100 locales comerciales que ofrecen las mejores marcas nacionales e internacionales en moda, tecnología, gastronomía y entretenimiento.</p>
        <p>Nuestra misión es brindar una experiencia de compra única, combinando calidad, variedad y las mejores promociones para nuestros clientes. Con el sistema de categorías de clientes, premiamos la fidelidad de quienes nos eligen día a día.</p>
      </div>
      <div class="col-lg-6 mb-4">
        <img src="https://via.placeholder.com/600x400/3498db/ffffff?text=Shopping+Rosario" class="img-fluid rounded shadow" alt="Shopping Rosario">
      </div>
    </div>
  </div>
</section>


<section class="py-5">
  <div class="container">
      <hr class="section-separator">
    <div class="text-center mb-5">
      <h2 class="section-title">Nuestros Valores</h2>
      <p class="section-subtitle">Los principios que nos guían cada día</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="feature-box">
          <i class="bi bi-heart-fill"></i>
          <h4>Pasión por el Cliente</h4>
          <p>Nos dedicamos a brindar la mejor experiencia de compra, siempre pensando en nuestros visitantes.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-box">
          <i class="bi bi-shield-check"></i>
          <h4>Confianza y Calidad</h4>
          <p>Seleccionamos cuidadosamente a nuestros locales para garantizar productos y servicios de excelencia.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-box">
          <i class="bi bi-lightbulb-fill"></i>
          <h4>Innovación</h4>
          <p>Constantemente incorporamos nuevas tecnologías para mejorar tu experiencia de compra.</p>
        </div>
      </div>
    </div>
  </div>
</section>


<section class="about-section py-4">
  <div class="container">
      <hr class="section-separator">
    <div class="text-center mb-5">
      <h2 class="section-title">Sistema de Promociones Digital</h2>
      <p class="section-subtitle">Innovación al servicio de tu ahorro</p>
    </div>
    <div class="row align-items-center">
      <div class="col-lg-6 mb-4 order-lg-2">
        <h3>¿Cómo funciona?</h3>
        <p>Desarrollamos un sistema digital único que permite a nuestros clientes acceder a promociones exclusivas de manera fácil y rápida desde cualquier dispositivo.</p>
        <ul class="list-unstyled">
          <li class="mb-3"><i class="bi bi-check-circle-fill text-success fs-5 me-2"></i><strong>Registro Simple:</strong> Creá tu cuenta en minutos</li>
          <li class="mb-3"><i class="bi bi-check-circle-fill text-success fs-5 me-2"></i><strong>Categorías Progresivas:</strong> Subí de nivel con tus compras</li>
          <li class="mb-3"><i class="bi bi-check-circle-fill text-success fs-5 me-2"></i><strong>Acceso Inmediato:</strong> Activá las promociones desde tu celular</li>
          <li class="mb-3"><i class="bi bi-check-circle-fill text-success fs-5 me-2"></i><strong>Sin Complicaciones:</strong> Todo 100% digital</li>
        </ul>
      </div>
      <div class="col-lg-6 mb-4 order-lg-1">
        <img src="https://via.placeholder.com/600x400/e74c3c/ffffff?text=Sistema+Digital" class="img-fluid rounded shadow" alt="Sistema Digital">
      </div>
    </div>
  </div>
</section>

<section class="py-5 bg-primaryColor text-white">
  <div class="container">
    <div class="row text-center">
      <div class="col-md-3 mb-4">
        <i class="bi bi-shop fs-1 mb-3"></i>
        <h2 class="display-4 fw-bold">100+</h2>
        <p class="lead">Locales Comerciales</p>
      </div>
      <div class="col-md-3 mb-4">
        <i class="bi bi-people fs-1 mb-3"></i>
        <h2 class="display-4 fw-bold">50K+</h2>
        <p class="lead">Clientes Registrados</p>
      </div>
      <div class="col-md-3 mb-4">
        <i class="bi bi-tag fs-1 mb-3"></i>
        <h2 class="display-4 fw-bold">500+</h2>
        <p class="lead">Promociones Activas</p>
      </div>
      <div class="col-md-3 mb-4">
        <i class="bi bi-calendar-check fs-1 mb-3"></i>
        <h2 class="display-4 fw-bold">15</h2>
        <p class="lead">Años de Experiencia</p>
      </div>
    </div>
  </div>
</section>

<section class="py-section">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">Nuestros Servicios</h2>
      <p class="section-subtitle">Todo lo que necesitas en un solo lugar</p>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card text-center">
          <div class="card-body">
            <i class="bi bi-p-square fs-1 text-primary mb-3"></i>
            <h5 class="card-title">Estacionamiento Gratuito</h5>
            <p class="card-text">Más de 1000 cocheras disponibles para tu comodidad.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center">
          <div class="card-body">
            <i class="bi bi-wifi fs-1 text-primary mb-3"></i>
            <h5 class="card-title">WiFi Gratis</h5>
            <p class="card-text">Internet de alta velocidad en todo el shopping.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center">
          <div class="card-body">
            <i class="bi bi-credit-card fs-1 text-primary mb-3"></i>
            <h5 class="card-title">Múltiples Medios de Pago</h5>
            <p class="card-text">Efectivo, tarjetas de crédito y débito en todos los locales.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center">
          <div class="card-body">
            <i class="bi bi-shield-check fs-1 text-primary mb-3"></i>
            <h5 class="card-title">Seguridad 24/7</h5>
            <p class="card-text">Personal de seguridad y cámaras en todo el complejo.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center">
          <div class="card-body">
            <i class="bi bi-universal-access fs-1 text-primary mb-3"></i>
            <h5 class="card-title">Accesibilidad Total</h5>
            <p class="card-text">Rampas, ascensores y baños adaptados.</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-center">
          <div class="card-body">
            <i class="bi bi-shop fs-1 text-primary mb-3"></i>
            <h5 class="card-title">Atención Personalizada</h5>
            <p class="card-text">Puntos de información en cada piso.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<section class="py-5">
  <div class="container text-center">
      <hr class="section-separator">
    <h2 class="mb-4">¿Te gustaría formar parte de nuestra comunidad?</h2>
    <p class="lead mb-4">Registrate y empezá a disfrutar de beneficios exclusivos</p>
    <a href="{{ $registerUrl }}" class="btn btn-primary btn-lg">
      <i class="bi bi-person-plus"></i> Crear Cuenta Gratis
    </a>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@endpush
