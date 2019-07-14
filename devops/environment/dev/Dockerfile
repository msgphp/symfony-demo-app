ARG image_nginx

# --- STAGE openssl infrastrucure

FROM alpine AS ssl-cert

RUN apk add --no-cache openssl

ARG password='NOT_SECURE'

RUN openssl genrsa -des3 -passout "pass:${password}" -out server.pass.key 2048
RUN openssl rsa -passin "pass:${password}" -in server.pass.key -out server.key; rm -f server.pass.key

ARG subject='/C=SS/ST=SS/L=The Internet/O=Symfony/CN=localhost'

RUN openssl req -new -passout "pass:${password}" -key server.key -out server.csr -subj "${subject}"
RUN openssl x509 -req -sha256 -days 3650 -in server.csr -signkey server.key -out server.crt

# --- STAGE web-ssl domain

FROM ${image_nginx} AS web-ssl

RUN mkdir -p /etc/nginx/ssl/
COPY --from=ssl-cert server.key server.crt /etc/nginx/ssl/
COPY nginx-proxy/conf.d/default.conf /etc/nginx/conf.d/

WORKDIR /app
