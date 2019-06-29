#!/usr/bin/env sh

staging_env=${STAGING_ENV:?}
ret=0

for service in $(find devops/docker -mindepth 1 -maxdepth 2 -name setup.sh); do
    sh -xc "cd $(dirname "${service}"); ./setup.sh" 2>&1
    [ $? -ne 0 ] && ret=1
done

if [ ${ret} -eq 0 ] && [ -f devops/environment/base/setup.sh ]; then
    sh -xc "cd devops/environment/base; ./setup.sh" 2>&1
    [ $? -ne 0 ] && ret=1
fi

if [ ${ret} -eq 0 ] && [ -f "devops/environment/${staging_env}/setup.sh" ]; then
    sh -xc "cd devops/environment/${staging_env}; ./setup.sh" 2>&1
    [ $? -ne 0 ] && ret=1
fi

exit ${ret}
