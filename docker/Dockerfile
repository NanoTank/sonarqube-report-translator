FROM php:8.1.3-fpm-alpine  AS system

RUN apk add --no-cache \
        gettext \
        libxslt \
        $PHPIZE_DEPS \

RUN pecl install xdebug

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
