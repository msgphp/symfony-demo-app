# Symfony & Docker

A template for new Symfony applications using Docker.

## Stack

- PHP-FPM
- MySQL
- NGINX

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

Cleanup the installer:

```bash
rm install.sh
```

Commit the initial files:

```bash
git add --all
git commit -m "Initial project structure"
```

## 1. Build Application

To create a default development build use:

```bash
make build
```

## 2. Install Application

Install the application initially using:

```bash
make install
```

## 3. Run Application

To run the application locally in development mode use:

```bash
make start

# application served at http://localhost:8080
```

Start a shell using:

```bash
make shell
```

## 4. Deploy Application

Create a production build using:

```bash
BUILD_ENV=prod make build
```

... 🏃

# Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md)
