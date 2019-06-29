# Symfony & Docker

A template for new Symfony applications using Docker.

## Default Stack

- PHP-FPM
- MySQL
- NGINX

## Features

```bash
sh -c "./install.sh; curl -I http://localhost:8080"
# ...
# X-Debug-Token-Link: http://localhost:8080/_profiler/079d79
```

- Bare Symfony defaults
- Testing and Quality-Assurance built-in
- `Makefile` based
- Multiple staging environments by design: `STAGING_ENV=prod make do-it`
- No hosting / release process assumptions

## The `devops/` Directory

The `devops/` directory holds all DevOps related concepts, thus separately from the application concern.

ℹ️ Don't mix&match `.env` files, considering each concern can rely on a different parsing technique ([ref](https://github.com/symfony/recipes/pull/487))

⚠️ Never commit secret values in `.env.dist` for non-dev concerns

### `devops/environment/`

The `environment/` directory holds all the application its staging environments, each containing a `docker-compose.yaml`
file at least. Its concern is to compose the final application logic based upon services.

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

All environments implicitly inherit from `base` due [Docker Compose `-f`], i.e. consider `docker-compose` being invoked
like:

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

### `devops/docker/`

The `docker/` directory holds all available application services, each containing a `Dockerfile` at least. Its concern is
to create the initial service its environment logic, required for the application to run.

👍 Consider a single service per concept a best practice, use [Docker multi-stage builds] for sub-concepts

ℹ️ A `Dockerfile` can obtain the targeted staging environment from a build argument, e.g. `ARG staging_env`

## Host Setup

To `COPY` files from outside the build context (e.g. pulled from an external source), the host OS needs a setup first.
During build `setup.sh` is automatically invoked first, from the following locations:

- `devops/docker/<service>/setup.sh`
- `devops/environment/base/setup.sh`
- `devops/environment/<targeted-staging-env>/setup.sh`

The following environment variables are automatically available in `setup.sh`:

- `.env` (see [Docker Compose `.env`])
- `$COMPOSE_PROJECT_NAME` (see [Docker Compose `$COMPOSE_PROJECT_NAME`])
- `$APP_DIR`
- `$STAGING_ENV`

To invoke the setup on-demand use:

```bash
make setup
```

## 0. Create Application

Bootstrap the initial skeleton first:

```bash
# latest stable
./install.sh

# specify version
SF=x.y ./install.sh
SF=x.y.z ./install.sh

# specify stability
STABILITY=dev ./install.sh

# skip initial commit
NO_COMMIT=1 ./install.sh
```

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

👍 Consider tagging images by VCS tag a best practice, e.g. `image:v1` is an artifact of the `v1` GIT tag

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

Install the application using:

```bash
make install
```

Consider a refresh (build/start/install) to install the application from scratch:

```bash
make refresh
```

## 4. Run Application

Visit the application at: http://localhost:8080 (`$NGINX_PORT`)

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

## Normalization

Normalize source files (e.g. `composer.json`) using:

```bash
make normalize
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

- https://github.com/api-platform/api-platform/blob/master/api/Dockerfile
- https://github.com/jakzal/docker-symfony-intl/blob/master/Dockerfile-intl

[DTAP]: https://en.wikipedia.org/wiki/Development,_testing,_acceptance_and_production
[Docker multi-stage builds]: https://docs.docker.com/develop/develop-images/multistage-build/
[Docker Compose `.env`]: https://docs.docker.com/compose/environment-variables/#the-env-file
[Docker Compose `$COMPOSE_PROJECT_NAME`]: https://docs.docker.com/compose/reference/envvars/#compose_project_name
[Docker Compose `-f`]: https://docs.docker.com/compose/extends/#multiple-compose-files
