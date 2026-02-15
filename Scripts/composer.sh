#!/usr/bin/env bash
#;
#;
#; Composer simplifies the process to add features like plugins
#;
#;
TOPDIR=$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")" && pwd)
# shellcheck source=lib/logging.sh
. "${TOPDIR}/Scripts/lib/logging.sh"
composer="${TOPDIR}/bin/composer"
if [ -n "$(command -v composer)" ]; then
        composer="composer"
elif [ ! -f "$composer" ]; then
        log_daemon_msg "Composer setup...\n"
	mkdir -p "$(dirname "$composer")"
	curl -sS https://getcomposer.org/installer | php -- --install-dir="$(dirname "$composer")" --filename=composer
fi
# shellcheck disable=SC2154
log_daemon_msg "Composer ${green}[OK]${nc}"
bash -c "${composer} --version"
#; update plugins and dependencies (composer install is good enough to check for updates)
"${composer}" "$@"
