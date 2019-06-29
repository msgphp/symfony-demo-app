#!/usr/bin/env sh

project=${COMPOSE_PROJECT_NAME:?}
app_dir=${APP_DIR:?}
staging_env=${STAGING_ENV:?}

echo "Building base infrastructure ..."

docker build --force-rm -q \
    --build-arg "image_php=${IMAGE_PHP:?}" \
    --build-arg "image_phpqa=${IMAGE_PHPQA:?}" \
    --build-arg "icu=${ICU:?}" \
    --tag "${project}/php" \
    "${app_dir}/devops/docker/php" && \

echo "Base infrastructure OK"
