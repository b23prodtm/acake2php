#!/usr/bin/env bash
set -e
wd="${MYPHPCMS_DIR}/e13/etc/"
src="constantes_template.properties"
dst="constantes.properties"
source ./Scripts/cp_bkp_old.sh $wd $src $dst
