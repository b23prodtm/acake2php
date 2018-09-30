#!/bin/sh
#;
#; colorful shell
nc='\033[0m'
red="\033[0;31m"
green="\033[0;32m"
orange="\033[0;33m"
cyan="\033[0;36m"

echo "${green}Fixing some file permissions...${nc}"
/bin/sh ./configure_tmp.sh
#; arguments are ./configure.sh -Y|-N [-p password -s salt -f filename]
#; if the full set of the arguments exists, there won't be any prompt in the shell
cd app/webroot/php_cms/e13/etc/
copies=0
while true
do
        # (1) prompt user, and read command line argument
        echo "${cyan}Step 1. Overwrite constantes.properties...\n${nc}"
        answer=$1
        case $answer in
               -[yY]* ) echo "Yes.\n"
                        answer="Y";;

               -[nN]* ) echo "No.\n"
                        break;;

               * )
                        read -p "Run the copy template script now (Y/N) ? " answer;;
        esac
        while [ -f constantes.properties.old-$copies ]
        do
        let copies++
        done
        # (2) handle the input we were given
        case $answer in
                [yY]* )
                        cp -v constantes.properties constantes.properties.old-$copies
                        cp -v constantes_template.properties constantes.properties
                        echo "Okay, just ran the shell script. Please, review the files.\n"
                        #quit while loop
                        break;;

                [nN]* ) break;;

                * )     echo "${red}Dude, just enter Y or N, please.\n${nc}";;
        esac
done
#; get hash password argv are -p password -s salt -f filename
echo "${cyan}Step 2. Get a hashed password with encryption, PHP encrypts.\n${nc}"
pass=$2
salt=$4
#; read password if not set as $3 argv
answer=$2
while true
do
        case $answer in
                -[yY]* ) echo "Yes.\n"
                        answer="Y";;

                -[nN]* ) echo "No.\n"
                        break;;

                * )
                        read -p "Run reset password script now (Y/N) ? " answer;;
        esac
         case $answer in
                [yY]* )
                        case $pass in
                               -[pP]* ) pass=$3;;

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
                       # read salt if not set as $5 argv
                       case $salt in
                               -[sS]* ) salt=$5;;

                               * )
                                       while [ "$salt" == "" ]
                                       do
                                       read -p "Please enter the salt word :" salt
                                       done;;
                       esac

                       hash_file="export_hash_password.sh"
                       php -f getHashPassword.php -- -p $pass -s $salt -f $hash_file
                       #; so that the shell can execute export file
                       chmod 777 $hash_file
                       echo "Saved in $hash_file .\n"
                       break;;
                [nN]* ) break;;

                * )     echo "${red}Dude, just enter Y or N, please.\n${nc}";;
        esac
        answer=$1
done
cd ../../../../../

# Know-How : In Openshift 3, configure a CakePhp-Mysql-persistent docker image. Set automatic deployment with _100%_ unavailability
# while deploying and _0_ surge pod in deployment advanced edit configuration tab.
# If it starts a build, it automatically scales deployments down to zero, and deploys and scales up when it's finished to build.
# Be sure that lib/Cake/Console/cake test app and Health checks should return gracefullly, or the pods get terminated after a short time.

echo "${cyan}Step 3. migrate database\n${nc}"
answer=$3
while true
do
        case $answer in
               -[yY]* ) echo "Yes.\n"
                        answer="Y";;
               -[nN]* ) echo "No.\n"
                        break;;

               * )
                        read -p "Run migrate database script now (Y/N) ? " answer;;
        esac

        case $answer in
                [yY]* ) /bin/sh ./migrate-database.sh -y -y -y
                        break;;
                [nN]* ) break;;
                * )     echo "${red}Dude, just enter Y or N, please.\n${nc}";;
        esac
        answer=$1
done;
