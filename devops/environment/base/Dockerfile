ARG image_nginx
ARG image_php
ARG image_phpqa

# --- STAGE nginx (infrastructure)

FROM ${image_nginx} AS nginx

COPY nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /app

# --- STAGE app (domain)

FROM ${image_php} AS app

# --- STAGE app-test (domain)

FROM ${image_phpqa} AS phpqa

FROM app AS app-test

COPY --from=phpqa /tools/php-cs-fixer /tools/
COPY --from=phpqa /tools/psalm /tools/
ENV PATH "${PATH}:/tools"
