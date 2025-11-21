# Base
FROM php:8.2-fpm-alpine

ARG WWWUSER=www-data
ARG WORKDIR=/var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_MEMORY_LIMIT=-1 \
  PATH=/root/.composer/vendor/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

# 1) Instalar paquetes de build y paquetes necesarios para compilar extensiones
RUN apk add --no-cache --virtual .build-deps \
  $PHPIZE_DEPS \
  autoconf \
  make \
  g++ \
  cmake \
  libxml2-dev \
  oniguruma-dev \
  zlib-dev \
  libzip-dev \
  libpng-dev \
  libjpeg-turbo-dev \
  freetype-dev \
  icu-dev \
  bash \
  git \
  curl \
  linux-headers \
  && apk add --no-cache \
  # Paquetes runtime que deben permanecer en la imagen (no dev)
  libzip \
  libpng \
  libjpeg-turbo \
  freetype \
  icu-libs \
  zlib \
  && docker-php-ext-configure gd --with-jpeg --with-freetype \
  && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip xml opcache \
  && apk add --no-cache nginx supervisor \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
  # eliminar sólo paquetes de build
  && apk del .build-deps \
  && rm -rf /var/cache/apk/* /tmp/* /root/.composer/cache

WORKDIR $WORKDIR

# Copiar composer files para cache
COPY composer.json composer.lock* ./

# Diagnóstico + instalación dependencias
RUN composer --version && php -v && php -m || true \
  && if [ -f composer.json ]; then \
  echo "Ejecutando composer install (COMPOSER_MEMORY_LIMIT=-1)"; \
  COMPOSER_MEMORY_LIMIT=-1 composer install --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-ansi || \
  (echo "Composer falló, ejecutando composer diagnose..." && composer diagnose && COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --no-progress) ; \
  else \
  echo "No hay composer.json, omitiendo composer install"; \
  fi

# Node (opcional para assets)
COPY package.json package-lock.json* ./
RUN if [ -f package.json ]; then npm ci --silent && (npm run build || npm run prod || true); fi

# Copiar aplicación
COPY . .

# Copiar configs de nginx/supervisor y entrypoint (si los usas)
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Permisos
RUN addgroup -g 1000 $WWWUSER || true \
  && adduser -D -u 1000 -G $WWWUSER -s /bin/sh $WWWUSER || true \
  && chown -R $WWWUSER:$WWWUSER $WORKDIR \
  && mkdir -p storage/framework storage/logs bootstrap/cache \
  && chown -R $WWWUSER:$WWWUSER storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

EXPOSE 80

ENV APP_ENV=production \
  APP_DEBUG=false \
  APP_KEY= \
  PORT=80 \
  LOG_CHANNEL=stderr

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["supervisord", "-n", "-c", "/etc/supervisord.conf"]