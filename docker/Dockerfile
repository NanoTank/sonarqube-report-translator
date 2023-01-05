FROM php:8.1-cli-alpine  AS system

RUN apk add --no-cache \
        gettext \
        libxslt \
        $PHPIZE_DEPS

RUN apk add --update linux-headers

RUN pecl install xdebug

RUN docker-php-ext-enable xdebug

COPY docker/xdebug.ini $PHP_INI_DIR/conf.d/docker-php-ext-xdebug_config.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY . /app

RUN composer install



