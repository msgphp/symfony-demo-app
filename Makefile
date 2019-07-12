ifndef PHP
	PHP=7.2
endif
ifndef PHPUNIT
	PHPUNIT=7.5
endif

qa_image=jakzal/phpqa:php${PHP}-alpine
composer_args=--prefer-dist --no-progress --no-interaction --no-suggest

dockerized=docker run --init -it --rm \
	-u $(shell id -u):$(shell id -g) \
	-v $(shell pwd):/app \
	-w /app
qa=${dockerized} \
	-e COMPOSER_CACHE_DIR=/app/var/composer \
	${qa_image}

# deps
install:
	${qa} composer install ${composer_args}
update:
	${qa} composer update ${composer_args}

# tests
phpunit:
	${qa} bin/phpunit

# code style
cs:
	${qa} php-cs-fixer fix --dry-run --verbose --diff
cs-fix:
	${qa} php-cs-fixer fix

# static analysis
psalm: install
	${qa} psalm --show-info=false
psalm-info: install
	${qa} psalm --show-info=true

# linting
lint-yaml:
	${dockerized} sdesbure/yamllint yamllint .yamllint .*.yml config/

# phpqa
qa-update:
	docker rmi -f ${qa_image}
	docker pull ${qa_image}

# misc
clean:
	git clean -dxf var/
smoke-test: clean update phpunit cs psalm
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
