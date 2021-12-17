FROM composer:latest as composer
FROM symfonycorp/cli:latest as symfonycli
FROM php:7.4-fpm-alpine

LABEL maintainer="Alexandr Fedorov <afedorov@onestep.ru>"

WORKDIR /app

ARG PHP_PORT=9000

ENV LANG=ru_RU.UTF-8 \
    LC_ALL=ru_RU.UTF-8 \
    LANGUAGE=ru_RU.UTF-8

ENV COMPOSER_MEMORY_LIMIT -1
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_PROCESS_TIMEOUT 2000
ENV COMPOSER_DISABLE_XDEBUG_WARN 1
ENV PHP_TIMEZONE="Europe/Moscow"
ARG PHP_INI_DIR="/usr/local/etc/php"
ARG PHPIZE_DEPS="autoconf dpkg-dev dpkg file g++ gcc libc-dev make pkgconf re2c"
ARG BUILD_DEPENDENSIES=""

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=symfonycli /symfony /usr/bin/symfony

RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN apk update && apk upgrade
RUN apk add --update --no-cache curl git gettext

### Console tools for dev
ARG APP_ENV="prod"
RUN if [[ "$APP_ENV" == "dev" ]]; then \
    apk add --update --no-cache nano htop net-tools mc; \
    fi

##### For build ext
#RUN apk add  gcc musl-dev make

## PHP extension
ENV DEPENDENSIES="libzip libzip-dev mysql-client libmcrypt libmcrypt-dev openssh-client icu-dev libxml2-dev libxslt-dev espeak php7-dev php7-opcache"
ENV EXTENSIONS="pdo pdo_mysql mysqli soap intl zip xsl bcmath xml sockets exif"

## PHP extension GD
#ENV DEPENDENSIES="${DEPENDENSIES} freetype-dev libjpeg-turbo-dev libpng-dev"
#ENV EXTENSIONS="${EXTENSIONS} gd"

RUN apk add --no-cache --virtual .build-deps ${PHPIZE_DEPS} ${BUILD_DEPENDENSIES} \
    && apk add --no-cache ${DEPENDENSIES}

RUN docker-php-ext-install ${EXTENSIONS}
RUN docker-php-ext-enable opcache

#
#RUN docker-php-ext-configure gd --with-freetype --with-jpeg # PHP extension GD
#

## XDEBUG
ARG PHP_XDEBUG_MODE=""
ARG PHP_XDEBUG_CLIENT_PORT=9003
ARG PHP_XDEBUG_CLIENT_HOST=host.docker.internal

COPY _docker/php-fpm/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini.template

RUN if [[ "$PHP_XDEBUG_MODE" != "" ]]; then \
      apk add --no-cache php7-xdebug; \
      pecl install xdebug; \
      docker-php-ext-enable xdebug; \
      export PHP_XDEBUG_MODE=$PHP_XDEBUG_MODE; \
      export PHP_XDEBUG_CLIENT_PORT=$PHP_XDEBUG_CLIENT_PORT; \
      export PHP_XDEBUG_CLIENT_HOST=$PHP_XDEBUG_CLIENT_HOST; \
      envsubst < /usr/local/etc/php/conf.d/xdebug.ini.template > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi

#local-php-security-checker
ARG PHP_SC_VERSION=''
RUN PHP_SC_VERSION=$(curl -s "https://api.github.com/repos/fabpot/local-php-security-checker/releases/latest" | grep '"tag_name":' | sed -E 's/.*"([^"]+)".*/\1/;s/^v//') && \
	curl -LSs https://github.com/fabpot/local-php-security-checker/releases/download/v${PHP_SC_VERSION}/local-php-security-checker_${PHP_SC_VERSION}_linux_amd64 > /usr/local/bin/local-php-security-checker && \
	chmod +x /usr/local/bin/local-php-security-checker && \
	unset PHP_SC_VERSION

# clean cache
RUN apk del .build-deps
RUN rm -rf /tmp/* && rm -rf /var/cache/apk/*

EXPOSE $PHP_PORT
EXPOSE $PHP_XDEBUG_CLIENT_PORT