ifndef BUILD_ENV
	BUILD_ENV=dev
endif
ifndef BUILD_ARGS
	BUILD_ARGS=
endif

build_args=${BUILD_ARGS} --build-arg BUILD_ENV=${BUILD_ENV} --force-rm
composer_args=--prefer-dist --no-progress --no-interaction --no-suggest

dc=docker-compose \
	-p $(shell basename $(shell pwd))_${BUILD_ENV} \
	-f devops/environment/base/docker-compose.yaml \
	-f devops/environment/${BUILD_ENV}/docker-compose.yaml \
	--project-directory devops/environment/${BUILD_ENV}
exec=${dc} exec -u $(shell id -u):$(shell id -g)
app=${exec} app
composer=${app} composer

# application
install:
	${composer} install ${composer_args}
install-dist:
	${composer} install ${composer_args} --no-scripts --no-dev
update:
	${composer} update ${composer_args}
update-recipes:
	${composer} symfony:sync-recipes --force
shell:
	${app} sh
mysql:
	${exec} db sh -c "mysql -u \$${MYSQL_USER} -p\$${MYSQL_PASSWORD} \$${MYSQL_DATABASE}"

# contributing
smoke-test:
	echo "noop"

# containers
start:
	${dc} up --no-build -d
restart:
	${dc} restart
refresh: build start install
stop:
	${dc} stop
quit:
	${dc} down

# images
setup:
	cp -n devops/environment/${BUILD_ENV}/.env.dist devops/environment/${BUILD_ENV}/.env
	sh -c "set -a && . devops/environment/${BUILD_ENV}/.env; export BUILD_ENV=${BUILD_ENV}; devops/setup.sh" 2>&1
build: setup quit
	${dc} build ${build_args}

# misc
exec:
	echo "${exec}"
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
