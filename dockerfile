# Use an official PHP runtime as a parent image
FROM php:8.2-fpm-alpine

# Arguments for build caching (optional)
ARG WWWUSER=www-data
ARG WORKDIR=/var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1 \
  PATH=/root/.composer/vendor/bin:/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

# Install system dependencies, PHP extensions, composer, node (for building assets), nginx and supervisor
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
  nodejs \
  npm \
  && docker-php-ext-configure gd --with-jpeg --with-freetype \
  && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip xml opcache \
  && apk add --no-cache nginx supervisor \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
  && apk del .build-deps \
  && rm -rf /var/cache/apk/* /tmp/* /root/.composer/cache

# Create system user and workdir
RUN addgroup -g 1000 $WWWUSER || true \
  && adduser -D -u 1000 -G $WWWUSER -s /bin/sh $WWWUSER || true

WORKDIR $WORKDIR

# Copy only composer files to leverage Docker cache
COPY composer.json composer.lock* ./

# Install PHP dependencies (vendor)
RUN composer install --prefer-dist --no-interaction --no-progress --no-scripts --optimize-autoloader || composer install --no-interaction --no-progress

# Copy Node files and build assets (if any)
COPY package.json package-lock.json* ./
RUN if [ -f package.json ]; then npm ci --silent && (npm run build || npm run prod || true); fi

# Copy the rest of the application
COPY . .

# Copy nginx and supervisor configs
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set permissions for Laravel
RUN chown -R $WWWUSER:$WWWUSER $WORKDIR \
  && mkdir -p storage/framework storage/logs bootstrap/cache \
  && chown -R $WWWUSER:$WWWUSER storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

# Expose port 80
EXPOSE 80

# Environment defaults (override in Render)
ENV APP_ENV=production \
  APP_DEBUG=false \
  APP_KEY= \
  PORT=80 \
  LOG_CHANNEL=stderr

# Entrypoint handles keys, storage link and optional migrations
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Start supervisord which will run php-fpm and nginx
CMD ["supervisord", "-n", "-c", "/etc/supervisord.conf"]