# Symfony & Docker

A template for new Symfony applications using Docker.

## Default Stack

- PHP-FPM
- NGINX
- MySQL (for development)

## Features

```bash
sh -c "./install.sh; curl -I http://localhost:8080"
# ...
# X-Debug-Token-Link: http://localhost:8080/_profiler/079d79
```

- Bare Symfony defaults
- Testing and Quality-Assurance built-in
- Out-of-the-box production optimized images
- Multiple staging environments by design
- No hosting / release process assumptions
- Decoupled "devops"

---

## The `devops/` Directory

The `devops/` directory holds all DevOps related concepts, thus separately from the application concern.

ℹ️ Don't mix&match `.env` files, considering each concern can rely on a different parsing technique ([ref](https://github.com/symfony/recipes/pull/487))

⚠️ Never commit secret values in `.env.dist` for non-dev concerns

### `devops/docker/`

The `docker/` directory holds all infrastructural services, each containing a `Dockerfile` at least. Its concern is to
setup an initial environment, required for the application to run.

ℹ️ A `Dockerfile` can obtain the targeted staging environment from a build argument (i.e. `ARG staging_env`)

👍 Consider a single service per concept a best practice, use [Docker multi-stage builds] for sub-concepts

### `devops/environment/`

The `environment/` directory holds all the application its staging environments, each containing a `docker-compose.yaml`
file at least. Its concern is to compose the final application logic based upon infrastructural services.

The following environment variables are automatically available in `docker-compose.yaml`:

- `.env` (see [Docker Compose `.env`])
- `$COMPOSE_PROJECT_NAME` (see [Docker Compose `$COMPOSE_PROJECT_NAME`])
- `$APP_DIR`
- `$STAGING_ENV`

To customize a staging environment use:

```bash
cp -n devops/environment/dev/.env.dist devops/environment/dev/.env
```

To create a new staging environment (e.g. `prod`) use:

```bash
cp -R devops/environment/dev devops/environment/prod
```

All environments implicitly inherit from `base` (due [Docker Compose `-f`]). Consider `docker-compose` always being
invoked as such:

```
docker-compose \
    -f devops/environment/base/docker-compose.yaml \
    -f devops/environment/$STAGING_ENV/docker-compose.yaml \
    --project-directory devops/environment/$STAGING_ENV
```

ℹ️ Do not confuse _staging environments_ with the _application environment_ (it's a matrix where conceptually each 
application environment can run on any staging environment, either remote or locally)

👍 Consider standard [DTAP] environments a best practice (this template assumes `dev`, `test`, `accept` and `prod`
respectively)

## Host Setup

To `COPY` files from outside the build context (e.g. pulled from an external source), the host OS can be setup first.

Prior to any build (to ensure freshness) `setup.sh` is automatically invoked from the following locations (in
order of execution):

- `devops/docker/<service>/setup.sh`
- `devops/environment/base/setup.sh`
- `devops/environment/<targeted-staging-env>/setup.sh`

The following environment variables are automatically available:

- `.env` (see [Docker Compose `.env`])
- `$COMPOSE_PROJECT_NAME` (see [Docker Compose `$COMPOSE_PROJECT_NAME`])
- `$APP_DIR`
- `$STAGING_ENV`

To invoke the setup on-demand use:

```bash
make setup
```

By default the `base` environment builds all infrastructural services from `devops/docker/` during setup. The artifact
images can then be leveraged by the application build (i.e. building from `docker-compose.yaml`).

ℹ️ This creates a two-way process and allows to scale infrastructure as needed (e.g. use prepared external images instead)

## Source Archives

During setup, the `devops/docker/archive` service creates a GIT archive from the current source code. This archive is
distributed using a minimal image (e.g `FROM scratch`) and allows application services to obtain and unpack it on demand
(e.g. `COPY --from=archive`).

Effectively this creates a final application image with source code included (e.g. production/distribution ready). For
development local volumes are configured.

---

## 0. Create Application

Bootstrap the initial skeleton first:

```bash
# latest stable
./install.sh

# specify stable
SF=x.y ./install.sh
SF=x.y.z ./install.sh

# install Symfony full
FULL=1 ./install.sh

# with initial GIT commit
GIT=1 ./install.sh
```

ℹ️ If available, the installer uses [Symfony client](https://symfony.com/download) or otherwise [Composer](https://getcomposer.org)  

⚠️ Cleanup the installer:

```bash
rm install.sh
```

And done 🎉, you can continue with [step 4](#4-run-application).

ℹ️ Start from [step 1](#1-build-application) after a fresh clone

## 1. Build Application

To create a default development build use:

```bash
make build
```

Build the application for a specific staging environment using:

```bash
STAGING_ENV=prod make build
```

### Tagging Images

After the build images are tagged `latest` by default. Any other form of tagging (e.g. semantic versioning) is out of 
scope of this template repository.

👍 Consider tagging images by VCS tag a best practice (e.g. `image:v1` is an artifact of the `v1` GIT tag)

### Naming Conventions

The default project name is `<project-dirname>_<staging-env>` by convention. Infrastructural services are "slash"
suffixed (e.g. `.../php`), whereas application services are "underscored" (e.g. `..._app` or `..._db`).

👍 Consider the project name a local reference, use `docker tag` for alternative (distribution) names (e.g. `org/product-name:v1`)

## Containers

Step 2-4 applies to running containers (with Docker Compose) from the images built previous, that is a working
application. Conceptually we can create a containerized landscape from each staging environment's perspective, using
`STAGING_ENV=prod make start` (see below).

The good part is, this will actually use the production optimized images (thus great for local testing). The bad part
however is, this may work completely different in production (e.g. with [Kubernetes]).

See also [What is a Container Orchestrator, Anyway?](https://containerjournal.com/2017/05/29/container-orchestrator-anyway)

👍 For a truly "dockerized" setup, consider Docker Compose files the source of truth, use e.g. `devops/environment/prod/kubernetes`
to store any specific concept configurations

👍 Ultimately, double configuration bookkeeping should be avoided (use environment variables, tools such as [Kompose],
etc.)

## 2. Start Application

To start the application locally in development mode use:

```bash
make start
```

Consider a restart to have fresh containers once started:

```bash
make restart
```

## 3. Install Application

Install the application for development using:

```bash
make install
```

Consider a refresh (build/start/install) to install the application from scratch:

```bash
make refresh
```

## 4. Run Application

Visit the application in development mode at: http://localhost:8080 (`$NGINX_PORT`)

Start a shell using:

```bash
make shell

# enter test app
SERVICE=app-test make shell
```

Start a MySQL client using:

```bash
make mysql

# enter test database
SERVICE=db-test make mysql
```

# Miscellaneous

## One-Off Commands

```bash
sh -c "$(make exec) app ls"
```

Alternatively, use `make run` to create a temporary container and run as `root` user by default.

```bash
sh -c "$(make run) --no-deps app whoami"
```

## Debug

Display current docker-compose configuration and/or its images using:

```bash
make composed-config
make composed-images
```

Follow service logs:

```bash
make log
```

## Verify Symfony Requirements

After any build it might be considered to verify if Symfony requirements are (still) met using:

```bash
make requirement-check
```

# Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md)

# References

- https://docs.docker.com/develop/develop-images/dockerfile_best-practices/
- https://cloud.google.com/blog/products/gcp/7-best-practices-for-building-containers
- https://github.com/api-platform/api-platform/blob/master/api/Dockerfile
- https://github.com/jakzal/docker-symfony-intl/blob/master/Dockerfile-intl

[DTAP]: https://en.wikipedia.org/wiki/Development,_testing,_acceptance_and_production
[Docker multi-stage builds]: https://docs.docker.com/develop/develop-images/multistage-build
[Docker Compose `.env`]: https://docs.docker.com/compose/environment-variables/#the-env-file
[Docker Compose `$COMPOSE_PROJECT_NAME`]: https://docs.docker.com/compose/reference/envvars/#compose_project_name
[Docker Compose `-f`]: https://docs.docker.com/compose/extends/#multiple-compose-files
[Kubernetes]: https://kubernetes.io
[Kompose]: https://kompose.io
