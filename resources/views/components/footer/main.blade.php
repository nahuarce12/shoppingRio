<footer class="bg-dark text-light py-5 mt-auto">
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-4">
        <h5 class="fw-semibold">Shopping Rosario</h5>
        <p class="mb-3">Tu centro comercial favorito con las mejores ofertas y promociones de la ciudad.</p>
        <div class="d-flex gap-3 fs-5">
          <a href="#" class="text-light" title="Facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-light" title="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-light" title="Twitter"><i class="bi bi-twitter"></i></a>
          <a href="#" class="text-light" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>

      <div class="col-md-4 mb-4">
        <h5 class="fw-semibold">Enlaces Rápidos</h5>
        <ul class="list-unstyled">
          <li><a class="text-light text-decoration-none" href="{{ Route::has('home.index') ? route('home.index') : url('/') }}">Inicio</a></li>
          <li><a class="text-light text-decoration-none" href="{{ Route::has('pages.locales') ? route('pages.locales') : url('#locales') }}">Locales</a></li>
          <li><a class="text-light text-decoration-none" href="{{ Route::has('pages.promociones') ? route('pages.promociones') : url('#promociones') }}">Promociones</a></li>
          <li><a class="text-light text-decoration-none" href="{{ Route::has('pages.about') ? route('pages.about') : url('#quienes-somos') }}">Quiénes Somos</a></li>
          <li><a class="text-light text-decoration-none" href="{{ Route::has('pages.contact') ? route('pages.contact') : url('#contacto') }}">Contacto</a></li>
        </ul>
      </div>

      <div class="col-md-4 mb-4">
        <h5 class="fw-semibold">Contacto</h5>
        <ul class="list-unstyled">
          <li><i class="bi bi-geo-alt me-2"></i> Av. Pellegrini 1234, Rosario</li>
          <li><i class="bi bi-telephone me-2"></i> (0341) 123-4567</li>
          <li><i class="bi bi-envelope me-2"></i> info@shoppingrosario.com</li>
          <li><i class="bi bi-clock me-2"></i> Lun - Dom: 10:00 - 22:00</li>
        </ul>
      </div>
    </div>

    <hr class="border-secondary">

    <div class="row">
      <div class="col-12 text-center">
        <small>&copy; {{ date('Y') }} {{ config('app.name', 'ShoppingRio') }}. Todos los derechos reservados. | Trabajo Final - Entornos Gráficos - UTN FRRO</small>
      </div>
    </div>
  </div>
</footer>