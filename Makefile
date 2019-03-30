ifndef PHP
	PHP=7.2
endif
ifndef PHPUNIT
	PHPUNIT=7.5
endif

dockerized=docker run --init -it --rm \
	-u $(shell id -u):$(shell id -g) \
	-v $(shell pwd):/app \
	-w /app
qa=${dockerized} \
	-e COMPOSER_CACHE_DIR=/app/var/composer \
	-e SYMFONY_PHPUNIT_DIR=/app/var/phpunit \
	-e SYMFONY_PHPUNIT_VERSION=${PHPUNIT} \
	jakzal/phpqa:php${PHP}-alpine
composer_args=--prefer-dist --no-progress --no-interaction --no-suggest

# deps
install: phpunit-install
	${qa} composer install ${composer_args}
update: phpunit-install
	${qa} composer update ${composer_args}

# tests
phpunit-install:
	${qa} simple-phpunit install
phpunit:
	${qa} simple-phpunit

# code style / static analysis
cs:
	${qa} php-cs-fixer fix --dry-run --verbose --diff
cs-fix:
	${qa} php-cs-fixer fix
sa: install
	${qa} phpstan analyse
	#${qa} psalm --show-info=false

# linting
lint-yaml:
	${dockerized} sdesbure/yamllint yamllint .yamllint .*.yml config/

# misc
clean:
	rm -rf var/cache var/log var/phpstan var/php-cs-fixer.cache
smoke-test: clean update phpunit cs sa
shell:
	${qa} /bin/sh
composer-normalize: install
	${qa} composer normalize
link: install
	if [ ! -d var/msgphp-src/.git ]; then git clone -o upstream git@github.com:msgphp/msgphp.git var/msgphp-src; fi
	${qa} composer install --working-dir=var/msgphp-src
	${qa} composer link --working-dir=var/msgphp-src ../..
	if [ ! -d var/symfony-src/.git ]; then git clone -o upstream git@github.com:symfony/symfony.git var/symfony-src; fi
	${qa} var/symfony-src/link .
