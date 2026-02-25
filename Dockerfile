FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    icu-dev \
    postgresql-dev \
    libzip-dev \
    rabbitmq-c-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    unzip \
    git

RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    libzip-dev \
    icu-dev \
    postgresql-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp

RUN docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    intl \
    zip \
    opcache \
    gd

RUN docker-php-ext-configure intl \
    && docker-php-ext-install \
    pdo_pgsql \
    intl \
    zip \
    opcache

RUN pecl install amqp \
    && docker-php-ext-enable amqp

RUN apk del $PHPIZE_DEPS

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html


COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-interaction

COPY . .

RUN composer dump-autoload --optimize

RUN chown -R www-data:www-data var
