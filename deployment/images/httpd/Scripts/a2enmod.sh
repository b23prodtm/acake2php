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
    unload_lines "$1" "$2" "$3" ""
}

unload_lines () {
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
# Examples
# log_daemon_msg mpm_event module loading
# load_module "mod_mpm_event.so"
# unload_module "mod_php${PHP_LIB_MAJOR}.so"
# unload "DirectoryIndex" "index.html" "php${PHP_LIB_MAJOR}-module.conf"
# unload_lines "<FilesMatch" ".php" "php${PHP_LIB_MAJOR}-module.conf" ",+3d"


