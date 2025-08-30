FROM php:8.4-fpm-alpine

WORKDIR /var/www

RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    bash \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mbstring exif pcntl bcmath gd zip opcache

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN addgroup -g 1000 www
RUN adduser -u 1000 -G www -s /bin/bash -D www

COPY . /var/www

COPY --chown=www:www . /var/www

USER www

EXPOSE 9000
CMD ["php-fpm"]
