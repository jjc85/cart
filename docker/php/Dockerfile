FROM php:8.3-fpm-alpine

RUN apk update && rm -rf /var/cache/apk/*

RUN apk add --no-cache bash

RUN apk add --no-cache --virtual .build-deps \
    git \
    build-base \
    autoconf \
    pkgconf \
    libtool \
    linux-headers

RUN pecl install xdebug

RUN XDEBUG_SO_PATH="$(find /usr/local/lib/php/extensions/ -name xdebug.so)"; \
    echo "zend_extension=$XDEBUG_SO_PATH" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug-loader.ini;

COPY ./docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN apk add --no-cache libxml2-dev libxslt-dev \
    && docker-php-ext-install dom \
    && docker-php-ext-install xsl \
    && docker-php-ext-install pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

EXPOSE 9000