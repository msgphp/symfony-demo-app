# Symfony & Docker

A template setup for new Symfony applications using Docker.

## Stack

- PHP-FPM
- MySQL
- NGINX

## Create Appliction

Bootstrap the initial skeleton application first:

```bash
./install.sh
rm install.sh
```

## Run Application

To run the application locally in development mode use:

```bash
make init

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
