ARG image_archive
ARG image_php
ARG image_phpqa
ARG image_nginx

# --- STAGE infrastructure

FROM ${image_archive} AS archive

FROM ${image_php} AS php

FROM ${image_phpqa} AS phpqa

FROM ${image_nginx} AS nginx

# --- STAGE domain

FROM php AS app

COPY --from=archive /archive.tgz /

WORKDIR /app

RUN tar --no-same-owner --no-same-permissions -zxf /archive.tgz -C .; rm -f /archive.tgz

ARG staging_env

RUN if [ "${staging_env}" != 'dev' ]; then \
		set -eux; \
		composer install --prefer-dist --no-progress --no-interaction --no-suggest --classmap-authoritative; \
		composer clear-cache; \
	fi

FROM app AS app-dev

COPY --from=app /app /app
COPY --from=phpqa /tools/php-cs-fixer /tools/
COPY --from=phpqa /tools/psalm /tools/
ENV PATH "${PATH}:/tools"

FROM app-dev AS app-test

FROM app AS app-accept

FROM app-accept AS app-prod

# --- STAGE web (domain)

FROM nginx AS web

COPY nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY --from=app /app /app

WORKDIR /app
