# Symfony & Docker

A template setup for new Symfony applications using Docker.

- PHP-FPM
- MySQL
- NGINX

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

# Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md)
