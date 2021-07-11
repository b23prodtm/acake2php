#!/usr/bin/env bash
set -e
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
MYPHPCMS_DIR=${MYPHPCMS_DIR:-'app/webroot/php-cms'}
wd="${TOPDIR}/${MYPHPCMS_DIR}/e13/etc/"
src="constantes_template.properties"
dst="constantes.properties"
# shellcheck source=cp_bkp_old.sh
. "${TOPDIR}/Scripts/cp_bkp_old.sh" "$wd" "$src" "$dst"
