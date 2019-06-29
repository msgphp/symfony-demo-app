#!/usr/bin/env sh

staging_env=${1:?}
current=$(devops/bin/archive.sh HEAD devops/archive/sha);
[ $? -ne 0 ] && exit 1

ln -sf "sha/$(basename "${current}")" "devops/archive/${staging_env}-current.tgz"
[ $? -ne 0 ] && exit 1

ret=0

for env in $(ls devops/environment/*/docker-compose.yaml 2>/dev/null); do
    ln -sf ../../archive "$(dirname "${env}")/archive"
    last=$?; [ ${last} -ne 0 ] && ret=${last}
done

exit ${ret}
