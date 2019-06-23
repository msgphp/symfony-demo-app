composed=docker-compose exec \
	-u $(shell id -u):$(shell id -g)

# containers
start:
	docker-compose up -d
restart:
	docker-compose restart
pause:
	docker-compose pause
unpause:
	docker-compose unpause
quit:
	docker-compose stop
force-quit:
	docker-compose down

# images
build: force-quit
	docker-compose build
force-build: force-quit
	docker-compose build --no-cache

# application
shell:
	${composed} php sh

# contributing
smoke-test:
	echo "noop"
