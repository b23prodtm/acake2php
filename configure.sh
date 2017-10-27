#!/bin/sh
#; arguements are ./configure.sh -Y|-N [-p password -s salt -f filename] 
#; if the full set of the arguments exists, there won't be any prompt in the shell
cd app/webroot/php-cms/e13/etc/
copies=0
while true
do
        # (1) prompt user, and read command line argument
        echo "Step 1. Overwrite constantes.properties..."
        answer=$1
        case $answer in
               -[yY]* ) echo "Yes.\n"
                        answer="Y";;
        
               -[nN]* ) echo "No.\n"
                        break;;

               * )
                        read -p "Run the copy template script now (Y/N) ? " answer;;
        esac
        while [ -f constantes.properties.old-$copies ] || [ -f constantes_local.properties.old-$copies ]
        do
        let copies++
        done
        # (2) handle the input we were given
        case $answer in
                [yY]* )                         
                        cp -v constantes.properties constantes.properties.old-$copies
                        cp -v constantes_template.properties constantes.properties
                        cp -v constantes_template.properties constantes_local.properties.old-$copies
                        cp -v constantes_template.properties constantes_local.properties
                        echo "Okay, just ran the shell script. Please, review the files.\n"
                        #quit while loop
                        break;;

                [nN]* ) break;;

                * )     echo "Dude, just enter Y or N, please.\n";;
        esac
done
#; get hash password argv are -p password -s salt -f filename
echo "Step 2. Get a hashed password with encryption, PHP encrypts.\n"
pass=$2
salt=$4
#; read password if not set as $3 argv
case $pass in
        -[pP]* ) pass=$3;;
        
        * )
                while true 
                do
                        read -p "Please enter a password :" pass
                        read -p "Please re-enter the password :" confirmpass
                        if [ "$pass" == "$confirmpass" ]; then
                                break
                        else
                                echo "Passwords don't match.\n"
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

cd ../../../../../

# Know-How : In Openshift 3, configure a CakePhp-Mysql-persistent docker image. Set automatic deployment with _100%_ unavailability
# while deploying and _0_ surge pod in deployment advanced edit configuration tab. 
# If it starts a build, it automatically scales deployments down to zero, and deploys and scales up when it's finished to build.
# Be sure that lib/Cake/Console/cake test app and Health checks should return gracefullly, or the pods get terminated after a short time.
