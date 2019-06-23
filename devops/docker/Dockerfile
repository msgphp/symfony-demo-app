# originally taken from https://github.com/api-platform/api-platform
ARG PHP_VERSION=7.3


# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine AS stage_php

RUN apk add --no-cache \
		acl \
		file \
		gettext \
		git \
	;

RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		libzip-dev \
		mysql-dev \
		zlib-dev \
	; \
	\
	docker-php-ext-configure zip --with-libzip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		pdo_mysql \
		zip \
	; \
	docker-php-ext-enable \
		opcache \
	; \
	\
	runDeps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .app-phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY php/conf.d/application.ini $PHP_INI_DIR/conf.d/application.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_CACHE_DIR=/tmp/composer
ENV COMPOSER_HOME=/composer
COPY php/composer.json /composer/composer.json
RUN set -eux; \
	composer global install --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/composer/vendor/bin"

WORKDIR /app

COPY php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]


# "nginx" stage
FROM nginx:alpine AS stage_nginx

COPY nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /app


# "mysql" stage
FROM mysql AS stage_mysql

COPY mysql/conf.d/my.cnf /etc/mysql/conf.d/my.cnf
