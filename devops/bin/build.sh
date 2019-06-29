#!/usr/bin/env sh

project=${1:?}
staging_env=${2:?}
current=$(devops/bin/archive.sh HEAD devops/archive/dist/sha);
[ $? -ne 0 ] && exit 1

ln -sf "sha/$(basename "${current}")" "devops/archive/dist/${staging_env}-current.tgz"
[ $? -ne 0 ] && exit 1

echo "Building ${staging_env} archive ..."

docker build --force-rm -q \
   --build-arg "archive=${staging_env}-current" \
    --tag "${project}/archive" \
    "devops/archive" && \

echo "Archive OK"
