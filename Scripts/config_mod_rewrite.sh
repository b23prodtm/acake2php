#!/usr/bin/env bash
set -eu
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
. init_functions .
CNF="/etc/apache2"
log_daemon_msg "Enable mod_rewrite"
if [ -f "${CNF}/httpd.conf" ]; then
  sed -i.old -E -e "/mod_rewrite.so/s/^#+//g" "${CNF}/httpd.conf"
  grep mod_rewrite.so < "${CNF}/httpd.conf"
else
  log_warning_msg "APACHE2 SERVER CONFIG: ${CNF}/httpd.conf file not found"
fi
log_daemon_msg "Add /etc/hosts $SERVER_NAME"
if [ -w "/etc/hosts" ]; then
  tmpfile=$(mktemp)
  sed -E -e "/127.0.0.1/s/(localhost)/\\1 ${SERVER_NAME} www.${SERVER_NAME}/" /etc/hosts > "$tmpfile"
  cat "$tmpfile" > /etc/hosts
else
  log_warning_msg "/etc/hosts file not found"
fi
