s/%%BALENA_MACHINE_NAME%%/raspberrypi3-64/g
s/(Dockerfile\.)[^\.]*/\1aarch64/g
s/%%BALENA_ARCH%%/aarch64/g
s/(BALENA_ARCH[=:-]+)[^$ }]+/\1aarch64/g
s#(IMG_TAG[=:-]+)[^$ }]+#\1v0.9.8#g
s#%%IMG_TAG%%#v0.9.8#g
s#(PRIMARY_HUB[=:-]+)[^$ }]+#\1betothreeprod/apache-php7#g
s#%%PRIMARY_HUB%%#betothreeprod/apache-php7#g
s#(PRIMARY_TAG[=:-]+)[^$ }]+#\1latest-aarch64#g
s#%%PRIMARY_TAG%%#latest-aarch64#g
s#(SECONDARY_HUB[=:-]+)[^$ }]+#\1lscr.io/linuxserver/mariadb#g
s#%%SECONDARY_HUB%%#lscr.io/linuxserver/mariadb#g
s#(SECONDARY_TAG[=:-]+)[^$ }]+#\1arm64v8-10.6.13#g
s#%%SECONDARY_TAG%%#arm64v8-10.6.13#g
s#(BALENA_ARCH[=:-]+)[^$ }]+#\1aarch64#g
s#%%BALENA_ARCH%%#aarch64#g
s#(BALENA_MACHINE_NAME[=:-]+)[^$ }]+#\1raspberrypi3-64#g
s#%%BALENA_MACHINE_NAME%%#raspberrypi3-64#g
