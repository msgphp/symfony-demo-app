#!/bin/sh

[ -f devops/environment/dev/.env ] && . devops/environment/dev/.env

SF="${SF:-}"
STABILITY="${STABILITY:-stable}"
SKELETON="${SKELETON:-symfony/website-skeleton}"

[ ! -z "${SF}" ] && [ $(echo "${SF}" | awk -F"." '{print NF-1}') -lt 2 ] && SF="${SF}.*"

echo "Starting installation ..."

[ ! -d ~/.composer ] && mkdir ~/.composer

dockerized="docker run --rm \
    -v ${HOME}/.composer:/tmp/composer \
    -v $(pwd):/app -w /app \
    -u $(id -u):$(id -g) \
    -e COMPOSER_HOME=/tmp/composer"
[ -t 1 ] && dockerized="${dockerized} -it"
tmp_dir=$(mktemp -d -t install-XXXXX --tmpdir=.)

${dockerized} composer create-project "${SKELETON} ${SF}" ${tmp_dir} -s "${STABILITY}" --no-install --remove-vcs && \
mv ${tmp_dir}/* . && \
rmdir ${tmp_dir} && \
make build start && \
rm public/index.php && \
echo "DATABASE_URL=mysql://${MYSQL_USER:-app}:${MYSQL_PASSWORD:-pass}@db:3306/${MYSQL_DATABASE:-app}" >> .env.dev.local && \
make install
