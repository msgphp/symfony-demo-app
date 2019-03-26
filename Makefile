ifndef PHP
	PHP=7.2
endif

dockerized=docker run --init -it --rm \
	-u $(shell id -u):$(shell id -g) \
	-v $(shell pwd):/app \
	-w /app
qa=${dockerized} \
	-e COMPOSER_CACHE_DIR=/app/var/composer \
	jakzal/phpqa:php${PHP}-alpine

phpunit=${qa} bin/phpunit
phpunit_coverage=${qa} phpdbg -qrr bin/phpunit
composer=${qa} composer
composer_args=--prefer-dist --no-progress --no-interaction --no-suggest

# deps
install: phpunit-install
	${composer} install ${composer_args}
update: phpunit-install
	${composer} update ${composer_args}

# tests
phpunit-install:
	${phpunit} install
phpunit:
	${phpunit}

# code style / static analysis
cs:
	${qa} php-cs-fixer fix --dry-run --verbose --diff --config=.php_cs.dist src/ tests/
cs-fix:
	${qa} php-cs-fixer fix --config=.php_cs.dist src/ tests/
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
	${composer} install --working-dir=var/msgphp-src
	${composer} link --working-dir=var/msgphp-src ../..
	if [ ! -d var/symfony-src/.git ]; then git clone -o upstream git@github.com:symfony/symfony.git var/symfony-src; fi
	${qa} var/symfony-src/link .
