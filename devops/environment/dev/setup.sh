#!/usr/bin/env sh

openssl="${APP_DIR:?}/devops/bin/openssl.sh"

cd secrets

if [ ! -f nginx.key ] || [ ! -f nginx.crt ]; then
    pass='pass:NOT_SECURE'
    subject='/C=SS/ST=SS/L=Internet/O=Symfony/CN=localhost'
    rm -f nginx.key nginx.crt && \
    ${openssl} genrsa -des3 -passout "${pass}" -out nginx.pass.key 2048 && \
    ${openssl} rsa -passin "${pass}" -in nginx.pass.key -out nginx.key && \
    ${openssl} req -new -passout "${pass}" -key nginx.key -out nginx.csr -subj "${subject}" && \
    ${openssl} x509 -req -sha256 -days 3650 -in nginx.csr -signkey nginx.key -out nginx.crt && \
    rm -f nginx.pass.key nginx.csr
    [ $? -ne 0 ] && cd - && exit 1
fi

cd - >/dev/null
