# Use an official PHP runtime (Alpine) with PHP-FPM
FROM php:8.2-fpm-alpine

ARG WWWUSER=www-data
ARG WORKDIR=/var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1 \
  COMPOSER_MEMORY_LIMIT=-1 \
  PATH=/root/.composer/vendor/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

# Install build deps, runtime libs and PHP extensions needed by Laravel.
# Keep runtime libraries (libpng, libzip, freetype, icu-libs, zlib) so extensions (gd, zip) can load.
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
  libzip \
  libpng \
  libjpeg-turbo \
  freetype \
  icu-libs \
  zlib \
  nodejs \
  npm \
  openssh-client \
  && docker-php-ext-configure gd --with-jpeg --with-freetype \
  && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip xml opcache \
  && apk add --no-cache nginx supervisor \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
  # remove build deps only (runtime libs kept)
  && apk del .build-deps \
  && rm -rf /var/cache/apk/* /tmp/* /root/.composer/cache

WORKDIR ${WORKDIR}

# Copy composer files first to leverage Docker layer cache for dependencies
COPY composer.json composer.lock* ./

# Install PHP dependencies without running composer scripts (those require artisan)
# This prevents composer from calling `php artisan` before the app files are present.
RUN composer --version && php -v && php -m || true \
  && if [ -f composer.json ]; then \
  echo "Ejecutando composer install --no-scripts (COMPOSER_MEMORY_LIMIT=-1)"; \
  COMPOSER_MEMORY_LIMIT=-1 composer install --prefer-dist --no-interaction --no-progress --no-scripts --optimize-autoloader --no-ansi || \
  (echo "Composer fallÃƒÂ³, ejecutando composer diagnose..." && composer diagnose && COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --no-progress --no-scripts) ; \
  else \
  echo "No hay composer.json, omitiendo composer install"; \
  fi

# Copy node manifest files (to leverage cache)
COPY package.json package-lock.json* ./

# Copy the rest of the application (including vite.config.js, resources/, etc.)
COPY . .

# Build frontend assets after app files are present so Vite can find vite.config.js
RUN if [ -f package.json ]; then \
  echo "Installing npm dependencies..." && \
  npm ci && \
  echo "Building frontend assets..." && \
  npm run build && \
  echo "Vite build completed. Checking public/build:" && \
  ls -la public/build/ || echo "public/build not found!" && \
  ls -la public/build/assets/ 2>/dev/null || echo "No assets folder"; \
  fi

# After the full app is present, regenerate optimized autoload (this does not run composer scripts that call artisan)
RUN if [ -f composer.json ]; then \
  COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize || true; \
  fi

# Copy nginx/supervisor configs and entrypoint (adjust paths if you store these elsewhere)
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
  && sed -i 's/\r$//' /usr/local/bin/entrypoint.sh

# Create system user and set permissions for Laravel
RUN addgroup -g 1000 ${WWWUSER} || true \
  && adduser -D -u 1000 -G ${WWWUSER} -s /bin/sh ${WWWUSER} || true \
  && chown -R ${WWWUSER}:${WWWUSER} ${WORKDIR} || true \
  && mkdir -p storage/framework storage/logs bootstrap/cache \
  && chown -R ${WWWUSER}:${WWWUSER} storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

EXPOSE 80

ENV APP_ENV=production \
  APP_DEBUG=false \
  APP_KEY= \
  PORT=80 \
  LOG_CHANNEL=stderr

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["supervisord", "-n", "-c", "/etc/supervisord.conf"]