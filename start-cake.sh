#!/bin/sh
source ./Scripts/bootstrap.sh
url="http://localhost:8080"
echo "Welcome homepage ${cyan}${url}${nc}"
echo "Debugging echoes ${cyan}${url}/admin/index.php${green}?debug=1&verbose=1${nc}"
echo "Alternate local tests ${cyan}${url}/admin/index.php${green}?local=1${nc}"
echo "Unit tests ${cyan}${url}/test.php${nc}"
echo "Turnoff flags ${cyan}${url}/admin/logoff.php${nc}"
echo "==============================================="
lib/Cake/Console/cake server -p 8080 $*
