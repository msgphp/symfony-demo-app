ARG image_nginx

FROM ${image_nginx} AS nginx

COPY nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /app
