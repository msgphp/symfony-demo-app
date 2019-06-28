ARG image_nginx

FROM ${image_nginx} AS nginx

COPY web/nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /app
