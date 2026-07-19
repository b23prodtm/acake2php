# docker-bake.hcl - Multi-platform builds with GHA cache

variable "REGISTRY" {
  default = "docker.io"
}

variable "DOCKER_ORG" {
  default = "betothreeprod"
}

# Alias for compatibility with manifest-push.sh
variable "REGISTRY_IMAGE" {
  default = "${DOCKER_ORG}"
}

variable "BAKE_TAG" {
  default = ""
}

variable "GITHUB_SHA" {
  default = ""
}

variable "PLATFORM" {
  default = "linux/amd64"
}

variable "BALENA_ARCH" {
  default = "x86_64"
}

group "default" {
  targets = ["php-fpm-x86_64", "httpd-x86_64", "db-x86_64", "balena-storage-x86_64"]
}

# Common target with GitHub Actions cache
target "common" {
  cache-from = ["type=gha"]
  cache-to   = ["type=gha,mode=max"]
}

# ============================================================================
# PHP-FPM SERVICE - CakePHP application (main service)
# ============================================================================
target "php-fpm" {
  inherits   = ["common"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:latest",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:${replace(BAKE_TAG, "/", "-")}" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:${GITHUB_SHA}" : ""
  ]
  secret = [
    "id=mysql_root_password,src=.balena/secrets/mysql_root_password",
    "id=mysql_user,src=.balena/secrets/mysql_user",
    "id=mysql_password,src=.balena/secrets/mysql_password",
    "id=mysql_database,src=.balena/secrets/mysql_database",
    "id=master_password,src=.balena/secrets/master_password",
  ]
}

# ============================================================================
# HTTPD SERVICE - Apache reverse proxy
# ============================================================================
target "httpd" {
  inherits   = ["common"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/httpd:latest",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/httpd:${replace(BAKE_TAG, "/", "-")}" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/httpd:${GITHUB_SHA}" : ""
  ]
}

# ============================================================================
# DB SERVICE - MariaDB database
# ============================================================================
target "db" {
  inherits   = ["common"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:latest",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:${replace(BAKE_TAG, "/", "-")}" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:${GITHUB_SHA}" : ""
  ]
}

# ============================================================================
# BALENA-STORAGE SERVICE
# ============================================================================
target "balena-storage" {
  inherits   = ["common"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:latest",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:${replace(BAKE_TAG, "/", "-")}" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:${GITHUB_SHA}" : ""
  ]
}

# ============================================================================
# MATRIX BUILDS - Per-architecture groups
# ============================================================================

group "armhf" {
  targets = ["php-fpm-armhf", "httpd-armhf", "db-armhf", "balena-storage-armhf"]
}

target "php-fpm-armhf" {
  inherits = ["php-fpm"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:arm32v7",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:${replace(BAKE_TAG, "/", "-")}-arm32v7" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:${GITHUB_SHA}-arm32v7" : ""
  ]
}

target "httpd-armhf" {
  inherits = ["httpd"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/httpd:arm32v7",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/httpd:${replace(BAKE_TAG, "/", "-")}-arm32v7" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/httpd:${GITHUB_SHA}-arm32v7" : ""
  ]
}

target "db-armhf" {
  inherits = ["db"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:arm32v7",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:${replace(BAKE_TAG, "/", "-")}-arm32v7" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:${GITHUB_SHA}-arm32v7" : ""
  ]
}

target "balena-storage-armhf" {
  inherits = ["balena-storage"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:arm32v7",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:${replace(BAKE_TAG, "/", "-")}-arm32v7" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:${GITHUB_SHA}-arm32v7" : ""
  ]
}

group "aarch64" {
  targets = ["php-fpm-aarch64", "httpd-aarch64", "db-aarch64", "balena-storage-aarch64"]
}

target "php-fpm-aarch64" {
  inherits = ["php-fpm"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:arm64v8",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:${replace(BAKE_TAG, "/", "-")}-arm64v8" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:${GITHUB_SHA}-arm64v8" : ""
  ]
}

target "httpd-aarch64" {
  inherits = ["httpd"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/httpd:arm64v8",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/httpd:${replace(BAKE_TAG, "/", "-")}-arm64v8" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/httpd:${GITHUB_SHA}-arm64v8" : ""
  ]
}

target "db-aarch64" {
  inherits = ["db"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:arm64v8",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:${replace(BAKE_TAG, "/", "-")}-arm64v8" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:${GITHUB_SHA}-arm64v8" : ""
  ]
}

target "balena-storage-aarch64" {
  inherits = ["balena-storage"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:arm64v8",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:${replace(BAKE_TAG, "/", "-")}-arm64v8" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:${GITHUB_SHA}-arm64v8" : ""
  ]
}

group "x86_64" {
  targets = ["php-fpm-x86_64", "httpd-x86_64", "db-x86_64", "balena-storage-x86_64"]
}

target "php-fpm-x86_64" {
  inherits = ["php-fpm"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:amd64",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:${replace(BAKE_TAG, "/", "-")}-amd64" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/php-fpm:${GITHUB_SHA}-amd64" : ""
  ]
}

target "httpd-x86_64" {
  inherits = ["httpd"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/httpd:amd64",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/httpd:${replace(BAKE_TAG, "/", "-")}-amd64" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/httpd:${GITHUB_SHA}-amd64" : ""
  ]
}

target "db-x86_64" {
  inherits = ["db"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:amd64",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:${replace(BAKE_TAG, "/", "-")}-amd64" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/mysqldb:${GITHUB_SHA}-amd64" : ""
  ]
}

target "balena-storage-x86_64" {
  inherits = ["balena-storage"]
  tags = [
    "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:amd64",
    BAKE_TAG != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:${replace(BAKE_TAG, "/", "-")}-amd64" : "",
    GITHUB_SHA != "" ? "${REGISTRY}/${REGISTRY_IMAGE}/balena-storage:${GITHUB_SHA}-amd64" : ""
  ]
}
