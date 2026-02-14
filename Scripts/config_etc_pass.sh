#!/usr/bin/env bash
#; [ $# -lt 1 ] && echo "Usage: $0 -p=<pass> -s=<hash> [-f=<exec_hash_file.sh>]" && exit 1
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
# shellcheck source=lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
pwd=$(pwd)
pass=""
hash=""
hash_file=""
MYPHPCMS_DIR=${MYPHPCMS_DIR:-'app/webroot/php-cms'}
dir="$TOPDIR/$MYPHPCMS_DIR/e13/etc/"
cd "$dir" || log_failure_msg "No such directory %s\n" "$dir"
# passed args from shell_prompt
while [ "$#" -gt 0 ]; do case $1 in
  -[pP]* )
      parse_arg_export "pass" "some password" "$@";;
  -[sS]* )
      parse_arg_export "hash" "some hash" "$@";;
  -[fF]* )
      parse_arg_export "hash_file" "a filename.sh" "$@";;
  *);;
esac; shift; done
#; read password if not set
if [ -z "$pass" ]; then while true; do
   read -r -p "Please enter a password :" pass
   echo -e "\n"
   read -r -p "Please re-enter the password :" confirmpass
   echo -e "\n"
   if [ "$pass" = "$confirmpass" ]; then
      break
   else
     # shellcheck disable=SC2154
      echo -e "${red}Passwords don't match.\n${nc}"
   fi
done; fi

# read hash if not set
if [ -z "$hash" ]; then while [ "$hash" = "" ]; do
   read -r -p "Please enter the hash word :" hash
done; fi
# read filename if not set
if [ -z "$hash_file" ]; then
    hash_file="export_hash_password.sh"
fi
printf "%\n" "MASTER_PASSWORD_HASH will be exported as an environment value..."
sleep 1
php -f getHashPassword.php -- -p "$pass" -s "$hash" -f "$hash_file"
#; so that the shell can execute export file
chmod 777 $hash_file
. "$hash_file"
log_daemon_msg "Saved in $hash_file .\n"
cd "$pwd" || log_failure_msg "No such directory %s\n" "$dir"
