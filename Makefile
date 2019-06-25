ifndef PHP;
	PHP=7.3
endif
ifndef NGINX
	NGINX=1.17
endif
ifndef MYSQL
	MYSQL=5.7
endif
ifndef BUILD_ENV
	BUILD_ENV=dev
endif
ifndef BUILD_ARGS
	BUILD_ARGS=
endif

build_args=${BUILD_ARGS} --force-rm \
	--build-arg BUILD_ENV=${BUILD_ENV} \
	--build-arg PHP=${PHP} \
	--build-arg NGINX=${NGINX} \
	--build-arg MYSQL=${MYSQL}
composer_args=--prefer-dist --no-progress --no-interaction --no-suggest

dc=docker-compose \
	-p $(shell basename $(shell pwd))_${BUILD_ENV} \
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
build: quit
	${dc} build ${build_args}

# misc
exec:
	echo "${exec}"
normalize:
	${composer} normalize
requirement-check:
	[ -f public/index.php.orig ] && echo "Requirement check in progress?" && exit 1
	${composer} require symfony/requirements-checker ${composer_args} --no-scripts
	mv public/index.php public/index.php.orig
	cp vendor/symfony/requirements-checker/public/check.php public/index.php
	${app} vendor/bin/requirements-checker
no-requirement-check:
	${composer} remove symfony/requirements-checker
	mv public/index.php.orig public/index.php

# debug
composed-config:
	${dc} config
composed-images:
	${dc} images
log:
	${dc} logs -f
