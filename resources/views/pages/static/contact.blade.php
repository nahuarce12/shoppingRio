@extends('layouts.app')

@section('title', 'Contacto - Shopping Rosario')
@section('meta_description', 'Contactate con el Shopping Rosario y recibí asistencia personalizada para tus consultas.')

@section('content')
<x-layout.breadcrumbs :items="[['label' => 'Contacto']]" />

<section class="py-5">
  <div class="container text-center">
    <h1 class="display-4 fw-bold text-primary">Contacto</h1>
    <p class="lead">Estamos aquí para ayudarte. Escribinos y te responderemos a la brevedad.</p>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="row g-4 mb-5">
      <div class="col-md-4">
        <div class="contact-info-box">
          <i class="bi bi-geo-alt-fill"></i>
          <h5>Dirección</h5>
          <p>Av. Pellegrini 1234<br>Rosario, Santa Fe<br>Argentina</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="contact-info-box">
          <i class="bi bi-telephone-fill"></i>
          <h5>Teléfono</h5>
          <p><strong>General:</strong> (0341) 123-4567<br><strong>WhatsApp:</strong> +54 9 341 123-4567<br><strong>Emergencias:</strong> (0341) 123-9999</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="contact-info-box">
          <i class="bi bi-envelope-fill"></i>
          <h5>Email</h5>
          <p><strong>General:</strong><br>info@shoppingrosario.com<br><strong>Soporte:</strong><br>soporte@shoppingrosario.com</p>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6 mb-4">
        <div class="card">
          <div class="card-body p-4">
            <h3 class="card-title mb-4"><i class="bi bi-chat-dots-fill"></i> Envíanos un Mensaje</h3>
            <form id="contactForm">
              <div class="mb-3">
                <label for="contact-name" class="form-label">Nombre Completo *</label>
                <input type="text" class="form-control" id="contact-name" name="nombre">
              </div>
              <div class="mb-3">
                <label for="contact-email" class="form-label">Email *</label>
                <input type="email" class="form-control" id="contact-email" name="email">
              </div>
              <div class="mb-3">
                <label for="contact-phone" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="contact-phone" name="telefono">
              </div>
              <div class="mb-3">
                <label for="contact-subject" class="form-label">Asunto *</label>
                <select class="form-select" id="contact-subject" name="asunto">
                  <option value="">Seleccione un asunto</option>
                  <option value="consulta">Consulta General</option>
                  <option value="promociones">Consulta sobre Promociones</option>
                  <option value="locales">Consulta sobre Locales</option>
                  <option value="registro">Problema con Registro</option>
                  <option value="reclamo">Reclamo</option>
                  <option value="sugerencia">Sugerencia</option>
                  <option value="otro">Otro</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="contact-message" class="form-label">Mensaje *</label>
                <textarea class="form-control" id="contact-message" name="mensaje" rows="5"></textarea>
              </div>
              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="contact-privacy">
                <label class="form-check-label" for="contact-privacy">Acepto la <a href="#">política de privacidad</a> *</label>
              </div>
              <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-send-fill"></i> Enviar Mensaje
              </button>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-6 mb-4">
        <div class="card mb-4" style="height: 400px;">
          <div class="card-body">
            <h4 class="card-title mb-3"><i class="bi bi-clock-fill"></i> Horarios de Atención</h4>
            <table class="table table-borderless">
              <tbody>
                <tr>
                  <td><strong>Lunes a Viernes:</strong></td>
                  <td>10:00 - 22:00</td>
                </tr>
                <tr>
                  <td><strong>Sábados:</strong></td>
                  <td>10:00 - 22:00</td>
                </tr>
                <tr>
                  <td><strong>Domingos y Feriados:</strong></td>
                  <td>12:00 - 22:00</td>
                </tr>
              </tbody>
            </table>
            <div class="alert alert-info" role="alert">
              <i class="bi bi-info-circle-fill"></i> <strong>Atención:</strong> Los horarios pueden variar según cada local comercial.
            </div>
          </div>
        </div>

        <div class="card" style="height: 400px;">
          <div class="card-body">
            <h4 class="card-title mb-3"><i class="bi bi-question-circle-fill"></i> Preguntas Frecuentes</h4>
            <div class="accordion" id="faqAccordion">
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">¿Cómo me registro en el sistema?</button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">Simplemente hacé clic en "Registrarse" en el menú superior y completá el formulario con tus datos. Recibirás un email de confirmación para activar tu cuenta.</div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">¿Cómo uso las promociones?</button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">Una vez registrado, visitá el local deseado, ingresá su código en tu cuenta y seleccioná la promoción que quieras usar. El local aprobará o rechazará tu solicitud.</div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">¿Hay estacionamiento disponible?</button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                  <div class="accordion-body">Sí, contamos con más de 1000 espacios de estacionamiento gratuito disponibles para nuestros visitantes.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<hr class="section-separator">

<section class="py-4">
  <div class="container">
    <h3 class="text-center mb-4"><i class="bi bi-map"></i> Cómo Llegar</h3>
    <div class="ratio ratio-16x9">
      <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3348.8534892803646!2d-60.653691254171505!3d-32.92846893544947!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMzLCsDU1JzQyLjkiUyA2MMKwMzknMTMuOCJX!5e0!3m2!1ses-419!2sar!4v1761410684877!5m2!1ses-419!2sar" width="600" height="450" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
    <div class="text-center mt-3">
      <p class="text-muted"><i class="bi bi-info-circle"></i> Ubicado en pleno centro de Rosario, con fácil acceso desde cualquier punto de la ciudad.</p>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <h3 class="text-center mb-4">Seguinos en Nuestras Redes</h3>
    <div class="row text-center">
      <div class="col-6 col-md-4 mb-3">
        <a href="#" class="text-decoration-none">
          <i class="bi bi-facebook fs-1 text-primary"></i>
          <p class="mt-2">Facebook</p>
        </a>
      </div>
      <div class="col-6 col-md-4 mb-3">
        <a href="#" class="text-decoration-none">
          <i class="bi bi-instagram fs-1 text-danger"></i>
          <p class="mt-2">Instagram</p>
        </a>
      </div>
      <div class="col-6 col-md-4 mb-3">
        <a href="#" class="text-decoration-none">
          <i class="bi bi-twitter fs-1 text-info"></i>
          <p class="mt-2">Twitter</p>
        </a>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
@vite('resources/js/frontoffice/main.js')
@endpush