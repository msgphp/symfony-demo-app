# Symfony & Docker

A template for new Symfony applications using Docker.

## Stack

- PHP-FPM
- MySQL
- NGINX

## Create Application

Bootstrap the initial skeleton first:

```bash
./install.sh
rm install.sh
```

Commit the initial files:

```bash
git add --all
git commit -m "Initial project structure"
```

## Install Application

Install the application initially using:

```bash
make install
```

## Run Application

To run the application locally in development mode use:

```bash
make start

# application served at http://localhost:8080
```

Start a shell using:

```bash
make shell
```

## Build Application

To create a default development build use:

```bash
make build
```

Create a production build using:

```bash
BUILD_ENV=prod make build
```

## Deploy Application

🏃

# Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md)
