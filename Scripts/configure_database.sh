#!/usr/bin/env bash
[ $# -lt 1 ] && echo "Usage : $0 [<file.template.or.php>]..." && exit 1
TOPDIR=$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")" && pwd)
sqlversion="5.7"
# shellcheck source=lib/logging.sh
. "${TOPDIR}/Scripts/lib/logging.sh"

function make_migration() {
        [ "$#" -lt 1 ] && printf "Usage: %s <in-schemafile.php>" "${FUNCNAME[0]}" && exit 1
	bash -c "php $TOPDIR/Scripts/schemaToMigration.php $1"
}

while [[ "$#" -gt 0 ]]; do case $1 in
	*.php|*.template)
		schemafile=$1
		file=$(echo "$schemafile" | cut -d . -f 1)
		make_migration "$schemafile"
		;;
	 *);;
esac; shift; done
