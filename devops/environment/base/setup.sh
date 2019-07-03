#!/usr/bin/env sh

project=${COMPOSE_PROJECT_NAME:?}
app_dir=${APP_DIR:?}
staging_env=${STAGING_ENV:?}

docker build --force-rm -q \
    --build-arg "archive=${staging_env}-current" \
    --tag "${project}/archive" \
    "${app_dir}/devops/docker/archive" \
&& \
docker build --force-rm -q \
    --build-arg "staging_env=${staging_env:?}" \
    --build-arg "image_php=${IMAGE_PHP:?}" \
    --build-arg "image_phpqa=${IMAGE_PHPQA:?}" \
    --build-arg "icu=${ICU:?}" \
    --tag "${project}/php" \
    "${app_dir}/devops/docker/php" \
;
