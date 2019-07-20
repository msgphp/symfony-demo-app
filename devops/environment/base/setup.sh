#!/usr/bin/env sh

project=${COMPOSE_PROJECT_NAME:?}
app_dir=${APP_DIR:?}
staging_env=${STAGING_ENV:?}
json="${app_dir}/devops/bin/json.sh"
openssl="${app_dir}/devops/bin/openssl.sh"

# docker infrastructure
docker build --force-rm \
    --build-arg "archive=${staging_env}-current" \
    --tag "${project}/archive" \
    "${app_dir}/devops/docker/archive" \
&& \
docker build --force-rm \
    --build-arg "staging_env=${staging_env:?}" \
    --build-arg "image_php=${IMAGE_PHP:?}" \
    --build-arg "image_phpqa=${IMAGE_PHPQA:?}" \
    --build-arg "icu=${ICU:?}" \
    --tag "${project}/php" \
    "${app_dir}/devops/docker/php" \
;

# file management
cd files

if [ "${staging_env}" = 'dev' ] && [ ! -f mhsendmail ]; then
    curl -sS -o mhsendmail --fail -L https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64
    [ $? -ne 0 ] && cd - && exit 1
fi

cd - >/dev/null

# secret management
cd "../${staging_env}/secrets"

${json} bucket.json "{\"APP_SECRET\": \"$(${openssl} rand -hex 16)\"}"
[ $? -ne 0 ] && cd - && exit 1

if [ ! -f jwt-private.pem ] || [ ! -f jwt-public.pem ]; then
    pass=$(${json} -r bucket.json JWT_PASSPHRASE)
    [ $? -ne 0 ] && cd - && exit 1
    [ -z "${pass}" ] && pass="$(${openssl} rand -hex 16)" && ${json} -f bucket.json "{\"JWT_PASSPHRASE\": \"${pass}\"}"
    rm -f jwt-private.pem jwt-public.pem && \
    ${openssl} genpkey -out jwt-private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass "pass:${pass}" && \
    ${openssl} pkey -in jwt-private.pem -out jwt-public.pem -pubout -passin "pass:${pass}"
    [ $? -ne 0 ] && cd - && exit 1
fi

cd - >/dev/null
