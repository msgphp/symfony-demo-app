# Symfony & Docker

A template for new Symfony applications using Docker.

## Default Stack

- PHP-FPM
- MySQL
- NGINX

## The `devops/` Directory

The `devops` directory holds all DevOps related concepts, thus separately from the application concern.

ℹ️ Don't mix&match `.env` files, considering each concern can rely on a different parsing technique ([ref](https://github.com/symfony/recipes/pull/487))

⚠️ Never commit secret values in `.env.dist` for non-dev concerns

### `devops/environment/`

The `environment` directory holds all the application its staging environments, each environment containing a
`docker-compose.yaml` file at least. Its concern is to compose the final application logic.

The following environment variables are automatically available in `docker-compose.yaml`.

- [`$COMPOSE_PROJECT_NAME`]
- `$APP_DIR`

ℹ️ Do not confuse _staging environments_ with the _application environment_. It's a matrix where conceptually each 
application environment can run on any staging environment, either remote or locally.

👍 Consider standard [DTAP] environments a best practice. This template assumes `dev`, `test`, `accept` and `prod`
respectively. All environments inherit from `base`.

To customize a staging environment use:

```bash
cp -n devops/environment/dev/.env.dist devops/environment/dev/.env
```

To create a new staging environment (e.g. `prod`) use:

```bash
cp -R devops/environment/base devops/environment/prod
```

### `devops/docker/`

The `docker` directory holds all available application services. Each directory represents a single service, containing
a `Dockerfile` at least. Its concern is to create the initial environment logic required for the application to run.

👍 Consider a single service per concept, to be used across all staging environments, a best practice

👍 Use multi-stage builds for sub-concepts

## Host Setup

To `COPY` files outside the build context (e.g. pulled from an external source), the host OS needs a setup first. During
build `setup.sh` is automatically invoked first, from the following locations:

- `devops/docker/<service>/setup.sh`
- `devops/environment/<staging-env>/setup.sh`

The target staging environment its variables are sourced from `devops/environment/<staging-env>/.env` and may be used.
The target staging environment itself and application directory are available using respectively `$STAGING_ENV` and
`$APP_DIR`.

ℹ️ A `Dockerfile` can obtain the target staging environment from a build argument, e.g. `ARG staging_env`

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
STAGING_ENV=prod ARGS='--no-cache --build-arg foo=bar' make build
```

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
[`$COMPOSE_PROJECT_NAME`]: https://docs.docker.com/compose/reference/envvars/#compose_project_name
