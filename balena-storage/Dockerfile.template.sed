s/%%BALENA_MACHINE_NAME%%/raspberrypi3/g
s/(Dockerfile\.)[^\.]*/\1armhf/g
s/%%BALENA_ARCH%%/armhf/g
s/(BALENA_ARCH[=:-]+)[^$ }]+/\1armhf/g
s#(IMG_TAG[=:-]+)[^$ }]+#\1latest#g
s#%%IMG_TAG%%#latest#g
s#(PRIMARY_HUB[=:-]+)[^$ }]+#\1#g
s#%%PRIMARY_HUB%%##g
s#(PRIMARY_TAG[=:-]+)[^$ }]+#\1#g
s#%%PRIMARY_TAG%%##g
s#(SECONDARY_HUB[=:-]+)[^$ }]+#\1linuxserver/mariadb#g
s#%%SECONDARY_HUB%%#linuxserver/mariadb#g
s#(SECONDARY_TAG[=:-]+)[^$ }]+#\1arm32v7-10.6.13#g
s#%%SECONDARY_TAG%%#arm32v7-10.6.13#g
s#(BALENA_ARCH[=:-]+)[^$ }]+#\1armhf#g
s#%%BALENA_ARCH%%#armhf#g
s#(BALENA_MACHINE_NAME[=:-]+)[^$ }]+#\1raspberrypi3#g
s#%%BALENA_MACHINE_NAME%%#raspberrypi3#g
