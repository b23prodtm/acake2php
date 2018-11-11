#!/bin/bash
pwd=`pwd`
cd app/webroot/php_cms/e13/etc/
pass=$5
salt=$7
file=$9
#; read password if not set as $5 argv
case $pass in
       -[pP]* ) pass=$6;;

       * )
               while true
               do
                       read -sp "Please enter a password :" pass
                       echo -e "\n"
                       read -sp "Please re-enter the password :" confirmpass
                       echo -e "\n"
                       if [ "$pass" == "$confirmpass" ]; then
                               break
                       else
                               echo -e "${red}Passwords don't match.\n${nc}"
                       fi
               done;;
esac
# read salt if not set as $7 argv
case $salt in
       -[sS]* ) salt=$8;;

       * )
               while [ "$salt" == "" ]
               do
               read -p "Please enter the salt word :" salt
               done;;
esac
# read filename if not set as $9 argv
case $file in
      -[fF]* )  hash_file=$10;;

      * )
                hash_file="export_hash_password.sh";;
esac
php -f getHashPassword.php -- -p $pass -s $salt -f $hash_file
#; so that the shell can execute export file
chmod 777 $hash_file
echo -e "Saved in $hash_file .\n"
cd $pwd
