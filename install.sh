#!/bin/sh
cd app/webroot/php-cms/e13/etc/
copies=0
while true
do
        # (1) prompt user, and read command line argument
        echo "It will overwrite constantes.properties."
        answer=$1
        case $answer in
               -[yY]* )
                        answer="Y";;
        
               -[nN]* ) break;;

               * )
                        read -p "Run the shell script now (Y/N) ? " answer;;
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
                        echo "Okay, just ran the shell script. Please, review the files."
                        #quit while loop
                        break;;

                [nN]* ) break;;

                * )     echo "Dude, just enter Y or N, please.";;
        esac
done
#; get hash password argv are -p password -s salt
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
                                echo "Passwords don't match."
                        fi
                done;;
esac
# read salt if not set as $5 argv
case $salt in
        -[sS]* ) salt=$5;;

        *)
                while [$salt == ""] 
                do
                let read -p "Please enter the 'salt' word :" salt 
                done;;
esac
                         
php -f getHashPassword.php -- -p $pass -s $salt

cd ../../../../../

# Know-How : In Openshift 3, configure a CakePhp-Mysql-persistent docker image. Disable automatic image stream deployment.
# Be sure that lib/Cake/Console/cake test app and Health checks should return gracefullly, or the pods get terminated after a short time.
# Scale down to zero, start build and deploy when finished to build. Then scale up to your pod usage needs.