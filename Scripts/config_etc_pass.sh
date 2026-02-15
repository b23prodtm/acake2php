#!/usr/bin/env bash
#; [ $# -lt 1 ] && echo "Usage: $0 -p <pass> -s <hash> [-f <hash_file>]" && exit 1
TOPDIR=$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")" && pwd)
# shellcheck source=lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# passed args from shell_prompt
parse_args "$@" <<EOF
option pass -p --password
option hash -s --salt
option hash_file -f --file
end
EOF
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
    hash_file="/run/secrets/master_password_hash"
fi
printf "%s\n" "MASTER_PASSWORD_HASH secret will be exported as an environment value..."
sleep 1
php -f "${TOPDIR}/app/webroot/php-cms/e13/etc/getHashPassword.php" -- -p "$pass" -s "$hash" -f "$hash_file"
export MASTER_PASSWORD_HASH="$(cat "$hash_file")"
log_daemon_msg "Saved in $hash_file .\n"
cd "$pwd" || log_failure_msg "No such directory %s\n" "$dir"
