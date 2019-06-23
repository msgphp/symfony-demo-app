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
compose=docker-compose \
	-p $(shell basename $(shell pwd)) \
	-f devops/docker/docker-compose.${BUILD_ENV}.yaml \
	--project-directory devops/docker
exec=${compose} exec -u $(shell id -u):$(shell id -g)

# containers
start:
	${compose} up -d
restart:
	${compose} restart
stop:
	${compose} stop
quit:
	${compose} down

# images
build: quit
	${compose} build ${build_args}

# application
shell:
	${exec} app sh

# contributing
smoke-test:
	echo "noop"
