#!/bin/bash
set -e
wd="app/webroot/php_cms/e13/etc/"
src="constantes_template.properties"
dst="constantes.properties"
source ./Scripts/cp_bkp_old.sh $wd $src $dst
