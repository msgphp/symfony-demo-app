# Symfony & Docker

A template for new Symfony applications using Docker.

## Stack

- PHP-FPM
- MySQL
- NGINX

## Local Environment

To customize the local environment use:

```bash
cp -n devops/docker/.env.dist devops/docker/.env
```

Modify the (new) `.env` file to your needs.

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

And done. The next steps only apply after a fresh clone of the project.

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

# by default application is served at http://localhost:8080
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
