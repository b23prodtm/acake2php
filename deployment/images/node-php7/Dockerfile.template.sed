s/%%BALENA_MACHINE_NAME%%/raspberrypi3/g
s/(Dockerfile\.)[^\.]*/\1armhf/g
s/%%BALENA_ARCH%%/armhf/g
s/(BALENA_ARCH[=:-]+)[^$ }]+/\1armhf/g
s#(IMG_TAG[=:-]+)[^$ }]+#\1latest#g
s#%%IMG_TAG%%#latest#g
s#(PRIMARY_HUB[=:-]+)[^$ }]+#\1betothreeprod/apache-php7#g
s#%%PRIMARY_HUB%%#betothreeprod/apache-php7#g
s#(SECONDARY_HUB[=:-]+)[^$ }]+#\1linuxserver/mariadb#g
s#%%SECONDARY_HUB%%#linuxserver/mariadb#g
s#(BALENA_ARCH[=:-]+)[^$ }]+#\1armhf#g
s#%%BALENA_ARCH%%#armhf#g
s#(BALENA_MACHINE_NAME[=:-]+)[^$ }]+#\1raspberrypi3#g
s#%%BALENA_MACHINE_NAME%%#raspberrypi3#g
