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

Commit the initial files:

```bash
git add --all
git commit -m "Initial project structure"
```

And done. The next steps only apply after a fresh clone of the project.

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

## 3. Run Application

To run the application locally in development mode use:

```bash
make start
```

By default the application is served at http://localhost:8080 (or the port as configured for the staging environment).

Start a shell using:

```bash
make shell
```

# Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md)
