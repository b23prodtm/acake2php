s/%%BALENA_MACHINE_NAME%%/intel-nuc/g
s/(Dockerfile\.)[^\.]*/\1x86_64/g
s/%%BALENA_ARCH%%/x86_64/g
s/(BALENA_ARCH[=:-]+)[^$ }]+/\1x86_64/g
s#(IMG_TAG[=:-]+)[^$ }]+#\1latest#g
s#%%IMG_TAG%%#latest#g
s#(PRIMARY_HUB[=:-]+)[^$ }]+#\1betothreeprod/apache-php7#g
s#%%PRIMARY_HUB%%#betothreeprod/apache-php7#g
s#(SECONDARY_HUB[=:-]+)[^$ }]+#\1lscr.io/linuxserver/mariadb#g
s#%%SECONDARY_HUB%%#lscr.io/linuxserver/mariadb#g
s#(BALENA_ARCH[=:-]+)[^$ }]+#\1x86_64#g
s#%%BALENA_ARCH%%#x86_64#g
s#(BALENA_MACHINE_NAME[=:-]+)[^$ }]+#\1intel-nuc#g
s#%%BALENA_MACHINE_NAME%%#intel-nuc#g
