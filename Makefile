ifndef PHP;
	PHP=7.3
endif
ifndef NGINX
	NGINX=1.17
endif
ifndef MYSQL
	MYSQL=8.0
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
dc=docker-compose \
	-p $(shell basename $(shell pwd))_${BUILD_ENV} \
	-f devops/docker/docker-compose.${BUILD_ENV}.yaml \
	--project-directory devops/docker
exec=${dc} exec -u $(shell id -u):$(shell id -g)
app=${exec} app
composer=${app} composer

# application
install:
	${composer} -h
update:
	${composer} -h
shell:
	${app} sh

# contributing
smoke-test:
	echo "noop"

# containers
init: build
	${dc} up --no-build -d
start:
	${dc} up --no-build -d
restart:
	${dc} restart
stop:
	${dc} stop
quit:
	${dc} down

# images
build: quit
	${dc} build ${build_args}

# misc
inspect-compose:
	${dc} config
