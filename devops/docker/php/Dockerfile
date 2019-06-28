# originally taken from https://github.com/api-platform/api-platform/blob/master/api/Dockerfile
# see also https://github.com/jakzal/docker-symfony-intl/blob/master/7.3/64.2/Dockerfile
ARG version

FROM php:${version}-fpm-alpine

ARG build_env
ARG icu

COPY icu/src-${icu}.tgz /tmp/icu.tgz

RUN apk add --no-cache \
		file \
		gettext \
		git \
	;

RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
		${PHPIZE_DEPS} \
		icu-dev \
		libzip-dev \
		mysql-dev \
		zlib-dev \
	; \
	\
	tar -zxf /tmp/icu.tgz -C /tmp; \
	cd /tmp/icu/source; CXXFLAGS="--std=c++0x" ./runConfigureICU Linux && make && make install; cd -; \
	rm -rf /tmp/icu /tmp/icu.tgz; \
	\
	docker-php-ext-configure intl; \
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
	run_deps="$( \
		scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
			| tr ',' '\n' \
			| sort -u \
			| awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .app-phpexts-rundeps ${run_deps}; \
	\
	apk del .build-deps

COPY conf.d/application.ini "${PHP_INI_DIR}/conf.d/"
RUN if [ "${build_env}" = 'dev' ]; then \
		ln -s "${PHP_INI_DIR}/php.ini-development" "${PHP_INI_DIR}/php.ini"; \
	else \
		ln -s "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini"; \
	fi

ENV COMPOSER_HOME=/composer
ENV COMPOSER_CACHE_DIR=/tmp/composer
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer /usr/bin/composer /usr/bin/
COPY composer/composer.json /composer/
RUN set -eux; \
	composer global install --prefer-dist --no-progress --no-suggest --classmap-authoritative; \
	composer clear-cache
ENV PATH="${PATH}:/composer/vendor/bin"

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

WORKDIR /app
ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]
