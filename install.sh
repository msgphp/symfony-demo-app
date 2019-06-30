#!/usr/bin/env sh

[ $# -ne 0 ] && echo "Usage: ${0}" && exit
[ "$(git status --porcelain)" ] && echo 'Working directory must be clean, please commit your changes first.' && exit 1

version=${SF:-''}
full=${FULL:-0}
commit=${GIT:-0}

echo 'Starting installation ...'

symfony=
composer=

if [ -x "$(command -v symfony)" ]; then
    symfony="$(which symfony)"
    echo "Using global Symfony installer at ${symfony} ..."
elif [ -x "$(command -v "${HOME}/.symfony/bin/symfony")" ]; then
    symfony="${HOME}/.symfony/bin/symfony"
    echo "Using local Symfony installer at ${symfony} ..."
elif [ -x "$(command -v composer)" ]; then
    composer="$(which composer)"
    echo "Using global Composer installer at ${composer} ..."
else
    mkdir -p "${HOME}/.composer"
    composer="docker run --rm \
        -v ${HOME}/.composer:/tmp/composer \
        -v $(pwd):/app -w /app \
        -u $(id -u):$(id -g) \
        -e COMPOSER_HOME=/tmp/composer"
    [ -t 1 ] && composer="${composer} -it"
    composer="${composer} composer"
    echo 'Using containerized Composer installer ...'
fi

cp -n devops/environment/dev/.env.dist devops/environment/dev/.env; . devops/environment/dev/.env
echo 'Development environment loaded ...'

tmp_dir=$(mktemp -d -t install-XXXXX --tmpdir=.); rm -rf "${tmp_dir}"

if [ -n "${symfony}" ]; then
    cmd="${symfony} new --no-git";
    [ -n "${version}" ] && cmd="${cmd} --version ${version}"
    [ ${full} -eq 1 ] && cmd="${cmd} --full"
    cmd="${cmd} ${tmp_dir}"
else
    skeleton='symfony/skeleton'; [ ${full} -eq 1 ] && skeleton='symfony/website-skeleton';
    v=${version}; [ ! -z "${v}" ] && [ $(echo "${v}" | awk -F"." '{print NF-1}') -lt 2 ] && v="${v}.*"
    cmd="${composer} create-project --remove-vcs ${skeleton} ${tmp_dir} ${v}"
fi

sh -xc "${cmd}"

[ $? -ne 0 ] && echo 'Installation failed ...' && rm -rf "${tmp_dir}" && exit 1

rm -f public/index.php && \
mv -f ${tmp_dir}/* . && cp -Rf "${tmp_dir}/." . && \
rm -rf "${tmp_dir}" && \

echo "DATABASE_URL=mysql://${MYSQL_USER:?}:${MYSQL_PASSWORD:?}@db/${MYSQL_DATABASE:?}" >> .env.dev.local && \
echo "DATABASE_URL=mysql://${MYSQL_USER:?}:${MYSQL_PASSWORD:?}@db-test/${MYSQL_DATABASE:?}" >> .env.test.local &&\

echo 'Initial source files created ...'

[ $? -ne 0 ] && echo 'Installation failed ...' && exit 1

if [ ${commit} -eq 1 ]; then
    [ ! -d .git ] && git init
    git add . && \
    git rm --cached "$0" && \
    git commit -m 'Initial project setup'
    [ $? -ne 0 ] && echo 'GIT commit failed ...'
fi

make build start
