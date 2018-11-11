#!/bin/bash
pwd=`pwd`
cd app/webroot/php_cms/e13/etc/
# passed args from shell_prompt
while [[ "$#" > 0 ]]; do case $1 in
  -[pP]* )
      pass=$2
      shift;;
  -[sS]* )
      salt=$2
      shift;;
  -[fF]* )
      hash_file=$2
      shift;;
  *) ;;
esac; shift; done
#; read password if not set
if [ -z $pass ]; then while true; do
   read -sp "Please enter a password :" pass
   echo -e "\n"
   read -sp "Please re-enter the password :" confirmpass
   echo -e "\n"
   if [ "$pass" == "$confirmpass" ]; then
           break
   else
           echo -e "${red}Passwords don't match.\n${nc}"
   fi
done; fi

# read salt if not set
if [ -z $salt ]; then while true; do
   while [ "$salt" == "" ]
   do
   read -p "Please enter the salt word :" salt
   done
done; fi
# read filename if not set
if [ -z $hash_file ]; then
    hash_file="export_hash_password.sh"
fi
php -f getHashPassword.php -- -p $pass -s $salt -f $hash_file
#; so that the shell can execute export file
chmod 777 $hash_file
echo -e "Saved in $hash_file .\n"
cd $pwd
