#!/bin/bash
source ./Scripts/lib/parsing.sh
saved="$@"
while [[ "$#" > 0 ]]; do case $1 in
  --help )
    echo "Usage: $0 [options] [-p|--sql-password=<password>]
            All options are arguments passed to the command lib/Cake/Console/cake server -p 8080
        -p, --sql-password=<password>
            Exports DATABASE_PASSWORD to bootargs.
        "
        exit 0;;
  -[pP]*|--sql-password*)
    export DATABASE_PASSWORD=$(parse_sql_password "$1" "$DATABASE_USER");;
  *);;
esac; shift; done
source ./Scripts/bootstrap.sh $saved
url="http://localhost:8080"
echo -e "Welcome homepage ${cyan}${url}${nc}"
echo -e "Debugging echoes ${cyan}${url}/admin/index.php${green}?debug=1&verbose=1${nc}"
echo -e "Another Test configuration ${cyan}${url}/admin/index.php${green}?test=1${nc}"
echo -e "Unit tests ${cyan}${url}/test.php${nc}"
echo -e "Turnoff flags ${cyan}${url}/admin/logoff.php${nc}"
echo -e "==============================================="
lib/Cake/Console/cake server -p 8080 $*
