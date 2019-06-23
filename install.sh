#!/bin/sh

[ ! -d ~/.composer ] && mkdir ~/.composer

dockerized="docker run --rm \
    -v ${HOME}/.composer:/tmp/composer \
    -v $(pwd):/app -w /app \
    -u $(id -u):$(id -g) \
    -e COMPOSER_HOME=/tmp/composer"
[ -t 1 ] && dockerized="${dockerized} -it"

tmp_dir=$(mktemp -d -t install-XXXXX --tmpdir=.)

${dockerized} composer create-project --no-install symfony/website-skeleton ${tmp_dir} && \
mv ${tmp_dir}/* . && \
rmdir ${tmp_dir} && \
make build start && \
rm public/index.php && \
echo "DATABASE_URL=mysql://app:pass@localhost:3306/app" > .env.dev && \
make install
