# see https://github.com/api-platform/api-platform
# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=7.3


# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine AS stage_php

# persistent / runtime deps
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
		postgresql-dev \
		zlib-dev \
	; \
	\
	docker-php-ext-configure zip --with-libzip; \
	docker-php-ext-install -j$(nproc) \
		intl \
		pdo_pgsql \
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

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
# install Symfony Flex globally to speed up download of Composer packages (parallelized prefetching)
RUN set -eux; \
	composer global require "symfony/flex" --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /app

# build for production
ARG APP_ENV=prod

# prevent the reinstallation of vendors at every changes in the source code
#COPY composer.json composer.lock symfony.lock ./
#RUN set -eux; \
#	composer install --prefer-dist --no-dev --no-scripts --no-progress --no-suggest; \
#	composer clear-cache

# do not use .env files in production
#COPY .env ./
#RUN composer dump-env prod; \
#	rm .env

# copy only specifically what we need
#COPY bin bin/
#COPY config config/
#COPY public public/
#COPY src src/

RUN set -eux; \
	mkdir -p var/cache var/log
#	mkdir -p var/cache var/log; \
#	composer dump-autoload --classmap-authoritative --no-dev; \
#	composer run-script --no-dev post-install-cmd; \
#	chmod +x bin/console; sync
VOLUME /app/var

COPY php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]


# "nginx" stage
# depends on the "php" stage above
FROM nginx:alpine AS stage_nginx

COPY nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /app

#COPY --from=stage_php /app/public public/
