#!/usr/bin/env sh

staging_env=${STAGING_ENV:?}
ret=0

for service in $(ls devops/docker/*/setup.sh 2>/dev/null); do
    sh -xc "cd $(dirname "${service}"); ./setup.sh" 2>&1
    last=$?; [ ${last} -ne 0 ] && ret=${last}
done

if [ ${ret} -eq 0 ] && [ -f devops/environment/base/setup.sh ]; then
    sh -xc "cd devops/environment/base; ./setup.sh" 2>&1
    last=$?; [ ${last} -ne 0 ] && ret=${last}
fi

if [ ${ret} -eq 0 ] && [ -f "devops/environment/${staging_env}/setup.sh" ]; then
    sh -xc "cd devops/environment/${staging_env}; ./setup.sh" 2>&1
    last=$?; [ ${last} -ne 0 ] && ret=${last}
fi

exit ${ret}
