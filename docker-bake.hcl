variable "DOCKER_ORG" {
  default = "betothreeprod"
}

variable "BAKE_TAG" {
  default = "latest"
}

variable "PLATFORM" {
  default = "linux/amd64"
}

variable "BALENA_ARCH" {
  default = "x86_64"
}

# Default group: all services for the selected BALENA_ARCH
group "default" {
  targets = ["db", "php-fpm", "httpd", "balena-storage"]
}

# Per-architecture groups used by the multi-arch CI workflow
group "armhf" {
  targets = ["db", "php-fpm", "httpd", "balena-storage"]
}

group "aarch64" {
  targets = ["db", "php-fpm", "httpd", "balena-storage"]
}

group "x86_64" {
  targets = ["db", "php-fpm", "httpd", "balena-storage"]
}

target "db" {
  context    = "mysqldb"
  dockerfile = "Dockerfile.${BALENA_ARCH}"
  platforms  = ["${PLATFORM}"]
  tags       = [
    "${DOCKER_ORG}/mysqldb:${BAKE_TAG}",
    equal(BAKE_TAG, "latest") ? "" : "${DOCKER_ORG}/mysqldb:latest"
  ]
  cache-from = ["type=gha,scope=db-${BALENA_ARCH}"]
  cache-to   = ["type=gha,scope=db-${BALENA_ARCH},mode=max"]
  args = {
    PUID = "1000"
    PGID = "1000"
  }
  secret = [
    "id=mysql_root_password,src=.balena/secrets/secret_mysql_root_password",
    "id=mysql_user,src=.balena/secrets/secret_mysql_user",
    "id=mysql_password,src=.balena/secrets/secret_mysql_password",
    "id=mysql_database,src=.balena/secrets/secret_mysql_database",
    "id=master_password,src=.balena/secrets/secret_master_password",
  ]
}

target "php-fpm" {
  context    = "."
  dockerfile = "Dockerfile.${BALENA_ARCH}"
  platforms  = ["${PLATFORM}"]
  tags       = [
    "${DOCKER_ORG}/php-fpm:${BAKE_TAG}",
    equal(BAKE_TAG, "latest") ? "" : "${DOCKER_ORG}/php-fpm:latest"
  ]
  cache-from = ["type=gha,scope=php-fpm-${BALENA_ARCH}"]
  cache-to   = ["type=gha,scope=php-fpm-${BALENA_ARCH},mode=max"]
  args = {
    PUID         = "1000"
    PGID         = "1000"
    MYPHPCMS_DIR = "app/webroot/php-cms"
    MYPHPCMS_LOG = "app/tmp/logs"
    HTDOCS       = "/var/www/cakephp"
  }
  secret = [
    "id=mysql_root_password,src=.balena/secrets/secret_mysql_root_password",
    "id=mysql_user,src=.balena/secrets/secret_mysql_user",
    "id=mysql_password,src=.balena/secrets/secret_mysql_password",
    "id=mysql_database,src=.balena/secrets/secret_mysql_database",
    "id=master_password,src=.balena/secrets/secret_master_password"
  ]
}

target "httpd" {
  context    = "httpd"
  dockerfile = "Dockerfile.${BALENA_ARCH}"
  platforms  = ["${PLATFORM}"]
  tags       = [
    "${DOCKER_ORG}/httpd:${BAKE_TAG}",
    equal(BAKE_TAG, "latest") ? "" : "${DOCKER_ORG}/httpd:latest"
  ]
  cache-from = ["type=gha,scope=httpd-${BALENA_ARCH}"]
  cache-to   = ["type=gha,scope=httpd-${BALENA_ARCH},mode=max"]
  args = {
    PUID   = "1000"
    PGID   = "1000"
    HTDOCS = "/var/www/cakephp"
  }
}

target "balena-storage" {
  context    = "balena-storage"
  dockerfile = "Dockerfile.${BALENA_ARCH}"
  platforms  = ["${PLATFORM}"]
  tags       = [
    "${DOCKER_ORG}/balena-storage:${BAKE_TAG}",
    equal(BAKE_TAG, "latest") ? "" : "${DOCKER_ORG}/balena-storage:latest"
  ]
  cache-from = ["type=gha,scope=balena-storage-${BALENA_ARCH}"]
  cache-to   = ["type=gha,scope=balena-storage-${BALENA_ARCH},mode=max"]
}
