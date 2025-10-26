#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
. init_functions .
CNF="/etc/apache2"
PHP_LIB_MAJOR=7
# ============= functions
load () {
    directive="$1"
    library="$2"
    file="${CNF}/$3"
    if [ -f "$file" ]; then
        sed -i.old -E -e "/$library/s/^#+${directive}//g" "$file"
        grep "$library" < "$file"
    else
        log_warning_msg "APACHE2 SERVER CONFIG: $file file not found"
    fi
}

load_module () {
    library="$1"
    load "LoadModule" "$library" "httpd.conf"    
}

unload () {
    directive="$1"
    library="$2"
    file="${CNF}/$3"
    lines_delete="$4"
    if [ -f "${file}" ]; then
        sed -i.old -E -e "/$library/s/(${directive}.*)/#\1/g${lines_delete}" "${file}"
        grep "$library" < "${file}"
    else
        log_warning_msg "APACHE2 SERVER CONFIG: ${file} file not found"
    fi
}

unload_module() {
    library="$1"
    unload "LoadModule" "$library" "php${PHP_LIB_MAJOR}-module.conf"
}
# =============

log_daemon_msg "Enable mod_rewrite"
load_module "mod_rewrite.so"
load_module "mod_mpm_event.so"
unload_module "mod_php${PHP_LIB_MAJOR}.so"
unload "DirectoryIndex" "index.html" "php${PHP_LIB_MAJOR}-module.conf"
unload "<FilesMatch" ".php" "php${PHP_LIB_MAJOR}-module.conf" ",+3d"

log_daemon_msg "Add /etc/hosts $SERVER_NAME"
if [ -w "/etc/hosts" ]; then
  tmpfile=$(mktemp)
  sed -E -e "/127.0.0.1/s/(localhost)/\\1 ${SERVER_NAME} www.${SERVER_NAME}/" /etc/hosts > "$tmpfile"
  cat "$tmpfile" > /etc/hosts
else
  log_warning_msg "/etc/hosts file not found"
fi
