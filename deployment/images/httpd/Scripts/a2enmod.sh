#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
. init_functions .
CNF="${SERVER_ROOT}/conf"
SSL="${SERVER_ROOT}/ssl"
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

gen_selfsigned_cert () {
    mkdir -p "${SSL}"
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout server.key -out server.crt \
    -subj "/C=FR/ST=Rhone/L=Vaulx-en-Velin/O=b23prodtm/CN=${SERVER_NAME}/BasicConstraints=CA:false/"
    cat server.crt server.key > server.pem
}
    
# Load and unload necessary modules directly in the Apache configuration.
# For example, instead of `a2enmod proxy`, manually add
# the following lines to the Apache configuration file.
log_daemon_msg "Configuration of Apache 2 ${CNF}/httpd.conf..."
load_module "mod_mpm_event.so"
load_module "mod_proxy.so"
load_module "mod_proxy_fcgi.so"
load_module "mod_ssl.so"
unload_module "mod_mpm_prefork.so"
unload_module "mod_rewrite.so"
log_daemon_msg "...Done."
if [ ! -f "${SSL}/server.pem" ]; then
    gen_selfsigned_cert
    cp -vf server.key "${SSL}/server.key"
    cp -vf server.pem "${SSL}/server.pem"
fi
apachectl configtest


