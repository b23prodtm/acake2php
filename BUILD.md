# Docker Bake Configuration

The x-bake configuration has been extracted from `docker-compose.yml` to `docker-bake.hcl`.

## Usage

### Building with Docker Buildx Bake

To build all images:
```bash
docker buildx bake -f docker-bake.hcl
```

To build a specific service:
```bash
docker buildx bake -f docker-bake.hcl db
docker buildx bake -f docker-bake.hcl php-fpm
docker buildx bake -f docker-bake.hcl httpd
docker buildx bake -f docker-bake.hcl balena-storage
```

### Setting Variables

You can override variables using environment variables or command-line flags:

```bash
# Using environment variables
export DOCKER_ORG=myorg
export BAKE_TAG=v1.0.0
export PLATFORM=linux/arm64
export BALENA_ARCH=aarch64
docker buildx bake -f docker-bake.hcl

# Using command-line
docker buildx bake -f docker-bake.hcl \
  --set "*.platform=linux/arm64" \
  --set "db.tags=myorg/mysqldb:v1.0.0"
```

### Multi-platform Builds

To build for multiple platforms:
```bash
docker buildx bake -f docker-bake.hcl \
  --set "*.platform=linux/amd64,linux/arm64"
```

### Push to Registry

To build and push to a registry:
```bash
docker buildx bake -f docker-bake.hcl --push
```

## Variables

- `DOCKER_ORG`: Docker organization/username (default: `betothreeprod`)
- `BAKE_TAG`: Tag for the images (default: `latest`)
- `PLATFORM`: Target platform (default: `linux/amd64`)
- `BALENA_ARCH`: Balena architecture (default: `amd64`)

## Docker Compose

The `docker-compose.yml` file has been cleaned up and no longer contains x-bake configuration. You can still use it for local development:

```bash
docker-compose up -d
```

Note: You'll need to use update_templates command line to filter out %%PLATFORM%% and %%BALENA_ARCH%% tags in docker-compose.template to docker-compose.yml.
