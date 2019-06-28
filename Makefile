ifndef STAGING_ENV
	STAGING_ENV=dev
endif

project=$(shell basename $(shell pwd))_${STAGING_ENV}
composer_args=--prefer-dist --no-progress --no-interaction --no-suggest

dc=COMPOSE_PROJECT_NAME=${project} APP_DIR=$(shell pwd)\
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
	${composer} symfony:sync-recipes --force
shell:
	${exec} $${SERVICE:-app} sh
mysql:
	${exec} $${SERVICE:-db} sh -c "mysql -u \$${MYSQL_USER} -p\$${MYSQL_PASSWORD} \$${MYSQL_DATABASE}"

# contributing
smoke-test:
	echo "todo"

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
	cp -n devops/environment/${STAGING_ENV}/.env.dist devops/environment/${STAGING_ENV}/.env
	sh -c "set -a && . devops/environment/${STAGING_ENV}/.env; export STAGING_ENV=${STAGING_ENV}; devops/setup.sh" 2>&1
build: setup quit
	if  [ ${STAGING_ENV} != dev ]; then sh -c "devops/archive.sh $(shell echo "$${GITREF:-HEAD}") devops/archive" 2>&1; fi
	${dc} build ${ARGS} --parallel --force-rm --build-arg staging_env=${STAGING_ENV}

# misc
exec:
	echo "${exec}"
run:
	echo "${dc} run --rm"
normalize:
	${composer} normalize
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
