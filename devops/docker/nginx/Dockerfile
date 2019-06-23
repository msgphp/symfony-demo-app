ARG NGINX

FROM nginx:${NGINX}-alpine AS stage_nginx

COPY conf.d/default.conf /etc/nginx/conf.d/default.conf

WORKDIR /app
