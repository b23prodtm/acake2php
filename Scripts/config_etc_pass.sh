#!/usr/bin/env bash
set -e
#; [ $# -lt 1 ] && echo "Usage: $0 -p <pass> -s <hash> [-f <hash_file>]" && exit 1
TOPDIR=$(cd "$(dirname "$(dirname "${BASH_SOURCE[0]}")")" && pwd)
# shellcheck source=lib/logging.sh
. "$TOPDIR/Scripts/lib/logging.sh"
# passed args from shell_prompt
parse_args_lazy "$@" <<EOF
option pass -p --password
option hash -s --salt
option hash_file -f --file
end
EOF
#; read password if not set
while [ ${#pass} -eq 0 ]; do
   read -r -p "Please enter a password :" pass
   echo -e "\n"
   read -r -p "Please re-enter the password :" confirmpass
   echo -e "\n"
   if [ "$pass" = "$confirmpass" ]; then
      break
   else
     # shellcheck disable=SC2154
      echo -e "Passwords don't match.\n"
   fi
done

# read hash if not set
while [ ${#hash} -eq 0 ]; do
   read -r -p "Please enter the hash word :" hash
done
# set filename if not set
if [ ${#hash_file} -eq 0 ]; then
    hash_file="master_password_hash"
fi
log_progress_msg "Master password auto configuration..."
cd "${TOPDIR}/app/webroot/php-cms/e13/etc/" || exit 1
log_progress_msg "Get hash from MASTER_PASSWORD..."
php -f "getHashPassword.php" -- -p "$pass" -s "$hash" -f "$hash_file"
MASTER_PASSWORD_HASH="$(cat "$hash_file")"
export MASTER_PASSWORD_HASH
cd "$pwd" || exit 1
log_success_msg "Done MASTER_PASSWORD_HASH was exported from $(pwd)/$hash_file"
