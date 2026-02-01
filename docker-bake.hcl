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
  default = "amd64"
}

# ---------------------------------------------------------------------------
# Secrets — values are NEVER baked into image layers.
# CI writes each one to a temp file and passes it via --secret id=...,src=...
# These variables exist only so bake can reference them in the secrets block.
# ---------------------------------------------------------------------------
variable "MYSQL_ROOT_PASSWORD" {
  default   = ""
  sensitive = true
}
variable "MYSQL_USER" {
  default   = ""
  sensitive = true
}
variable "MYSQL_PASSWORD" {
  default   = ""
  sensitive = true
}
variable "HASH_PASSWORD" {
  default   = ""
  sensitive = true
}

group "default" {
  targets = ["db", "php-fpm", "httpd", "balena-storage"]
}

target "db" {
  context    = "deployment/images/mysqldb"
  dockerfile = "Dockerfile.${BALENA_ARCH}"
  platforms  = ["${PLATFORM}"]
  tags       = [
    "${DOCKER_ORG}/mysqldb:latest",
    "${DOCKER_ORG}/mysqldb:${BAKE_TAG}"
  ]
  args = {
    PUID = "1000"
    PGID = "1000"
  }
  secrets = [
    "id=mysql_root_password,env=MYSQL_ROOT_PASSWORD",
    "id=mysql_user,env=MYSQL_USER",
    "id=mysql_password,env=MYSQL_PASSWORD",
  ]
}

target "php-fpm" {
  context    = "."
  dockerfile = "Dockerfile.${BALENA_ARCH}"
  platforms  = ["${PLATFORM}"]
  tags       = [
    "${DOCKER_ORG}/php-fpm:latest",
    "${DOCKER_ORG}/php-fpm:${BAKE_TAG}"
  ]
  args = {
    PUID        = "1000"
    PGID        = "1000"
    MYPHPCMS_DIR = "app/webroot/php-cms"
    MYPHPCMS_LOG = "app/tmp/logs"
    HTDOCS      = "/var/www/html"
  }
  secrets = [
    "id=mysql_root_password,env=MYSQL_ROOT_PASSWORD",
    "id=mysql_user,env=MYSQL_USER",
    "id=mysql_password,env=MYSQL_PASSWORD",
    "id=hash_password,env=HASH_PASSWORD",
  ]
}

target "httpd" {
  context    = "deployment/images/httpd"
  dockerfile = "Dockerfile.${BALENA_ARCH}"
  platforms  = ["${PLATFORM}"]
  tags       = [
    "${DOCKER_ORG}/httpd:latest",
    "${DOCKER_ORG}/httpd:${BAKE_TAG}"
  ]
  args = {
    PUID   = "1000"
    PGID   = "1000"
    HTDOCS = "/var/www/html"
  }
}

target "balena-storage" {
  context    = "balena-storage"
  dockerfile = "Dockerfile.${BALENA_ARCH}"
  platforms  = ["${PLATFORM}"]
  tags       = [
    "${DOCKER_ORG}/balena-storage:latest",
    "${DOCKER_ORG}/balena-storage:${BAKE_TAG}"
  ]
}
