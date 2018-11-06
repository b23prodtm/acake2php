#!/bin/sh
#; colorful shell
nc='\033[0m'
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"
cd app/webroot/php_cms/e13/etc/
copies=0
pass=$1
salt=$3
file=$5
#; read password if not set as $2 argv
case $pass in
       -[pP]* ) pass=$2;;

       * )
               while true
               do
                       read -sp "Please enter a password :" pass
                       echo "\n"
                       read -sp "Please re-enter the password :" confirmpass
                       echo "\n"
                       if [ "$pass" == "$confirmpass" ]; then
                               break
                       else
                               echo "${red}Passwords don't match.\n${nc}"
                       fi
               done;;
esac
# read salt if not set as $4 argv
case $salt in
       -[sS]* ) salt=$4;;

       * )
               while [ "$salt" == "" ]
               do
               read -p "Please enter the salt word :" salt
               done;;
esac
case $file in
      -[fF]* )  hash_file=$6;;

      * )
                hash_file="export_hash_password.sh";;
esac
php -f getHashPassword.php -- -p $pass -s $salt -f $hash_file
#; so that the shell can execute export file
chmod 777 $hash_file
echo "Saved in $hash_file .\n"
cd ../../../../../
