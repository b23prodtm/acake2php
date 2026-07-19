# Docker Bake Configuration

The per-architecture compose files still provide the concrete build inputs, while `docker-bake.hcl` layers tags, secrets, groups, and cache settings on top.

## Usage

### Building with Docker Buildx Bake

To build all images, e.g. x86_64 (linux/amd64):
```bash
balena_deploy . x86_64 3 0
ln -sf docker-compose.x86_64 docker-compose.yml
docker buildx bake -f docker-compose.yml -f docker-bake.hcl
```

To build a specific service:
```bash
docker buildx bake -f docker-compose.yml -f docker-bake.hcl db
docker buildx bake -f docker-compose.yml -f docker-bake.hcl php-fpm
docker buildx bake -f docker-compose.yml -f docker-bake.hcl httpd
docker buildx bake -f docker-compose.yml -f docker-bake.hcl balena-storage
```

### Setting Variables

You can override variables using environment variables or command-line flags:

```bash
# Using environment variables
export DOCKER_ORG=myorg
export BAKE_TAG=v1.0.0
export PLATFORM=linux/amd64
export BALENA_ARCH=x86_64

# Using command-line
balena_deploy . x86_64 3 0
ln -sf docker-compose.x86_64 docker-compose.yml
docker buildx bake -f docker-compose.yml -f docker-bake.hcl \
  --set "*.platform=linux/amd64" \
  --set "db.tags=myorg/mysqldb:v1.0.0"
```

### Cross-platform Builds

To build for other architecture platforms, e.g aarch64 (linux/arm64):
```bash
balena_deploy . aarch64 3 0
ln -sf docker-compose.aarch64 docker-compose.yml
docker buildx bake -f docker-compose.yml -f docker-bake.hcl \
  --set "*.platform=linux/arm64"
```
Multi-arch parallel builds aren't available. Only set 1 platform build at a time!
This is due to the project structure (*.env, multiple Dockerfiles, etc.)

### Push to Registry

To build and push to a registry, e.g. x86_64 (linux/amd64):
```bash
balena_deploy . x86_64 3 0
ln -sf docker-compose.x86_64 docker-compose.yml
docker buildx bake -f docker-compose.yml -f docker-bake.hcl --push
```

## Variables

- `DOCKER_ORG`: Docker organization/username (default: `betothreeprod`)
- `BAKE_TAG`: Tag for the images (default: `latest`)
- `PLATFORM`: Target platform (default: `linux/amd64`)
- `BALENA_ARCH`: Balena architecture (default: `amd64`)

## Docker Compose

Use `Scripts/update-templates.sh` (or `balena_deploy`) to refresh the arch-specific compose files, then point `docker-compose.yml` at the desired one before baking or running local compose commands:

```bash
ln -sf docker-compose.x86_64 docker-compose.yml
docker-compose up -d
```

Note: `docker buildx bake` depends on the filtered `docker-compose.*` files, so link the matching `docker-compose.<arch>` into `docker-compose.yml` first.
