s/%%BALENA_MACHINE_NAME%%/raspberrypi3-64/g
s/(Dockerfile\.)[^\.]*/\1aarch64/g
s/%%BALENA_ARCH%%/aarch64/g
s/(BALENA_ARCH[=:-]+)[^$ }]+/\1aarch64/g
s#(PLATFORM[=:-]+)[^$ }]+#\1linux/arm64#g
s#%%PLATFORM%%#linux/arm64#g
s#(IMG_TAG[=:-]+)[^$ }]+#\1latest#g
s#%%IMG_TAG%%#latest#g
s#(SECONDARY_HUB[=:-]+)[^$ }]+#\1linuxserver/mariadb#g
s#%%SECONDARY_HUB%%#linuxserver/mariadb#g
s#(SECONDARY_TAG[=:-]+)[^$ }]+#\1arm64v8-10.6.13#g
s#%%SECONDARY_TAG%%#arm64v8-10.6.13#g
s#(BALENA_ARCH[=:-]+)[^$ }]+#\1aarch64#g
s#%%BALENA_ARCH%%#aarch64#g
s#(BALENA_MACHINE_NAME[=:-]+)[^$ }]+#\1raspberrypi3-64#g
s#%%BALENA_MACHINE_NAME%%#raspberrypi3-64#g
s#(PHP_EXTENSIONS[=:-]+)[^$ }]+#\1#g
s#%%PHP_EXTENSIONS%%##g
