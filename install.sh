#!/bin/sh
cd app/webroot/php-cms/e13/etc/
while true
do
        # (1) prompt user, and read command line argument
        echo "It will overwrite constantes.properties. (Y/N) ?"
        read -p "Run the shell script now ? " answer

        # (2) handle the input we were given
        case $answer in
                [yY]* ) 
                        cp constantes_template.properties constantes.properties
                        cp constantes_template.properties constantes_local.properties
                        echo "Okay, just ran the shell script."
                        break;;

                [nN]* ) exit;;

                * )     echo "Dude, just enter Y or N, please.";;
  esac
done
cd ../../../../../

# Know-How : In Openshift 3, configure a CakePhp-Mysql-persistent docker image. Disable automatic image stream deployment.
# Be sure that lib/Cake/Console/cake test app and Health checks should return gracefullly, or the pods get terminated after a short time.
# Scale down to zero, start build and deploy when finished to build. Then scale up to your pod usage needs.