# Symfony & Docker

A template for new Symfony applications using Docker.

## Default Stack

- PHP-FPM
- MySQL
- NGINX

## Environment

To customize a staging environment use:

```bash
cp -n devops/environment/dev/.env.dist devops/environment/dev/.env
```

To create a new staging environment (e.g. `prod`) use:

```bash
cp -R devops/environment/dev devops/environment/prod
```

⚠️ Never commit secret values in `.env.dist` for non-dev environments.

ℹ️ Consider standard "DTAP" environments (Development, Testing, Acceptance and Production) a best practice.

> This template by default assumes `dev` and `prod` for respectively Development and Production.
> If you follow a different naming convention this needs to be accounted for in various template files (e.g. `install.sh`, `Dockerfile`).

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
```

⚠️ Cleanup the installer:

```bash
rm install.sh
```

Commit the initial project files:

```bash
git add --all
git commit -m "Initial project structure"
```

And done. Continue to step 4 (step 1-3 only apply after a fresh clone).

## 1. Build Application

To create a default development build use:

```bash
make build
```

Build the application for a specific staging environment using:

```bash
BUILD_ENV=prod make build
```

## 2. Install Application

Install the application using:

```bash
make install
```

## 3. Start Application

To start the application locally in development mode use:

```bash
make start
```

## 4. Run Application

Visit the application at: http://localhost:8080 (or the port as configured in the `dev` environment).

Start a shell using:

```bash
make shell
```

Start a MySQL client using:

```bash
make mysql
```

# Miscellaneous

## Run a One-Off Command

```bash
sh -c "$(make exec) app ls"
```

## Normalization

Normalize source files (e.g. `composer.json`) using:

```bash
make normalize
```

## Debug

See current docker-compose configuration and images using:

```bash
make composed-config
make composed-images
```

Follow service logs:

```bash
make log
```

## Verify Symfony Requirements

After a build verify if Symfony requirements are (still) met using:

```bash
make requirement-check
```

Fix any issues shown in the CLI. Then open `index.php` and comment out the `REMOTE_ADDR` check.

Visit the application in your browser to verify web and fix any remaining issues. If all good, continue using:

```bash
make no-requirement-check
```

# Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md)
