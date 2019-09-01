ifndef STAGING_ENV
	STAGING_ENV=dev
endif

app_dir=$(shell pwd)
project=$(shell basename ${app_dir})_${STAGING_ENV}
composer_args=--prefer-dist --no-progress --no-interaction --no-suggest

dockerized=docker run --init -it --rm \
	-u $(shell id -u):$(shell id -g) \
	-v $(shell pwd):/app \
	-w /app
dc=COMPOSE_PROJECT_NAME=${project} APP_DIR=${app_dir} STAGING_ENV=${STAGING_ENV} \
	docker-compose \
	-f devops/environment/base/docker-compose.yaml \
	-f devops/environment/${STAGING_ENV}/docker-compose.yaml \
	--project-directory devops/environment/${STAGING_ENV}
exec=${dc} exec -u $(shell id -u):$(shell id -g)
app=${exec} app
app_console=${app} bin/console
composer=${app} composer

# application
install:
	${composer} install ${composer_args}
update:
	${composer} update ${composer_args}
update-recipes:
	rm symfony.lock
	${composer} symfony:sync-recipes --force
shell:
	${exec} $${SERVICE:-app} sh -c "if [ -f /run/secrets/env_bucket ]; then set -a && . /run/secrets/env_bucket; fi; sh"
mysql:
	${exec} $${SERVICE:-db} sh -c "mysql -u \$${MYSQL_USER} -p\$${MYSQL_PASSWORD} \$${MYSQL_DATABASE}"
db-migrate:
	${app_console} doctrine:database:create --if-not-exists
	${app_console} doctrine:migrations:migrate --allow-no-migration -n
db-sync: db-migrate
	${app_console} doctrine:schema:update --force
db-fixtures: db-sync
	${app_console} doctrine:fixtures:load -n
api-sync:
	${app_console} projection:synchronize

# containers
start:
	${dc} up --no-build -d
restart:
	${dc} restart
refresh: build start install
stop:
	${dc} stop
quit:
	${dc} down --remove-orphans

# images
setup:
	devops/bin/setup.sh "${STAGING_ENV}" "${app_dir}" "${project}"
build: setup quit
	${dc} build --parallel --force-rm --build-arg staging_env=${STAGING_ENV}

# tests
phpunit:
	${app} bin/phpunit

# code style
cs:
	${app} php-cs-fixer fix --dry-run --verbose --diff
cs-fix:
	${app} php-cs-fixer fix

# static analysis
psalm: install
	${app} psalm --show-info=false
psalm-info: install
	${app} psalm --show-info=true

# linting
lint-yaml:
	${dockerized} sdesbure/yamllint yamllint .yamllint .*.yml config/

# devops
devops-init:
	git remote add devops git@github.com:ro0NL/symfony-docker.git
devops-merge:
	git fetch devops master
	git merge --no-commit --no-ff --allow-unrelated-histories devops/master

# misc
clean:
	git clean -dxf var/
smoke-test: clean install phpunit cs psalm
link: install
	if [ ! -d var/msgphp-src/.git ]; then git clone -o upstream git@github.com:msgphp/msgphp.git var/msgphp-src; fi
	${app} composer install --working-dir=var/msgphp-src
	${app} composer link --working-dir=var/msgphp-src ../..
	if [ ! -d var/symfony-src/.git ]; then git clone -o upstream git@github.com:symfony/symfony.git var/symfony-src; fi
	${app} var/symfony-src/link .
exec:
	echo "${exec}"
run:
	echo "${dc} run --rm"
requirement-check:
	${composer} require symfony/requirements-checker ${composer_args} --no-scripts -q
	${app} vendor/bin/requirements-checker
	${composer} remove symfony/requirements-checker -q

# debug
composed-config:
	${dc} config
composed-images:
	${dc} images
log:
	${dc} logs -f
