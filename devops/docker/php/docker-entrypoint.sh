#!/usr/bin/env sh
# originally taken from https://github.com/api-platform/api-platform/blob/master/api/docker/php/docker-entrypoint.sh
set -e

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi

exec docker-php-entrypoint "$@"
