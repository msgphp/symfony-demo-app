ARG image_archive
ARG image_nginx
ARG image_php
ARG image_phpqa

FROM ${image_archive} AS archive
FROM ${image_phpqa} AS phpqa

# --- STAGE nginx (infrastructure)

FROM ${image_nginx} AS nginx

COPY nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

# --- STAGE app (domain)

FROM ${image_php} AS app

COPY --from=archive /archive.tgz /

WORKDIR /app
RUN tar --no-same-owner --no-same-permissions -zxf /archive.tgz -C .; rm -f /archive.tgz

# --- STAGE app-test (domain)

FROM app AS app-test

COPY --from=app /app /app
COPY --from=phpqa /tools/php-cs-fixer /tools/
COPY --from=phpqa /tools/psalm /tools/
ENV PATH "${PATH}:/tools"

# --- STAGE web (domain)

FROM nginx AS web

COPY --from=app /app /app

WORKDIR /app
