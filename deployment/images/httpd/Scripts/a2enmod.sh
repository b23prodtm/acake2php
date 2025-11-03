#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
. init_functions .
CNF="${SERVER_ROOT}/conf"
# ============= functions
load () {
    directive="$1"
    library="$2"
    file="${CNF}/$3"
    if [ -f "$file" ]; then
        sed -i -E -e "/$library/s/^#+(${directive}.*)/\1/" "$file"
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
    unload_lines "$1" "$2" "$3" "g"
}

unload_lines () {
    directive="$1"
    library="$2"
    file="${CNF}/$3"
    lines_delete="$4"
    if [ -f "${file}" ]; then
        sed -i -E -e "/$library/s/(${directive}.*)/#\1/${lines_delete}" "${file}"
        grep "$library" < "${file}"
    else
        log_warning_msg "APACHE2 SERVER CONFIG: ${file} file not found"
    fi
}

unload_module() {
    library="$1"
    unload "LoadModule" "$library" "httpd.conf"
}

# Load and unload necessary modules directly in the Apache configuration.
# For example, instead of `a2enmod proxy`, manually add
# the following lines to the Apache configuration file.
log_daemon_msg "Configuration of Apache 2 ${CNF}/httpd.conf..."
load_module "mod_mpm_event.so"
load_module "mod_proxy.so"
load_module "mod_proxy_fcgi.so"
unload_module "mod_mpm_prefork.so"
unload_module "mod_rewrite.so"
# Change default User and Group
user="$USER"
group="www-data"
sed -i -E -e "#User#s#www-data#$user#g -e #Group#s#www-data#$group#g" "${CNF}/httpd.conf"
grep User < "${CNF}/httpd.conf"
grep Group < "${CNF}/httpd.conf"
log_daemon_msg "...Done."

