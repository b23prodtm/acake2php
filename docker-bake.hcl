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
  secret = [
    "id=mysql_root_password,src=.balena/secrets/secret_mysql_root_password",
    "id=mysql_user,src=.balena/secrets/secret_mysql_user",
    "id=mysql_password,src=.balena/secrets/secret_mysql_password",
    "id=mysql_database,src=.balena/secrets/secret_mysql_database",
    "id=master_password,src=.balena/secrets/secret_master_password"
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
