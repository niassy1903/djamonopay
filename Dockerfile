

FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./

RUN npm ci

COPY resources ./resources

COPY public/assets ./public/assets

COPY vite.config.js postcss.config.js tailwind.config.js ./

RUN npm run build


FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        libonig-dev \
        unzip \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        exif \
        gd \
        mbstring \
        pdo_mysql \
        pcntl \
        zip \
    && rm -rf /var/lib/apt/lists/*


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

COPY --from=frontend /app/public/build ./public/build

RUN composer install \
        --no-dev \
        --no-interaction \
        --no-progress \
        --prefer-dist \
        --optimize-autoloader \
    && a2enmod rewrite \
    && rm /etc/apache2/sites-enabled/000-default.conf \
    && cp docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf \
    && cp docker/ports.conf /etc/apache2/ports.conf \
    && chmod +x docker/entrypoint.sh \
    && mkdir -p \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && php artisan storage:link \
    && chown -R www-data:www-data storage bootstrap/cache


ENV APP_ENV=production \
    APP_DEBUG=false \
    PORT=10000

EXPOSE 10000

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]