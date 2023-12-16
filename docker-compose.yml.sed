s/%%BALENA_MACHINE_NAME%%/generic-aarch64/g
s/(Dockerfile\.)[^\.]*/\1aarch64/g
s/%%BALENA_ARCH%%/aarch64/g
s/(DKR_ARCH[=:-]+)[^$ }]+/\1aarch64/g
s#(IMG_TAG[=:-]+)[^$ }]+#\1latest#g
s#%%IMG_TAG%%#latest#g
s#(PRIMARY_HUB[=:-]+)[^$ }]+#\1betothreeprod/apache-php7#g
s#%%PRIMARY_HUB%%#betothreeprod/apache-php7#g
s#(SECONDARY_HUB[=:-]+)[^$ }]+#\1betothreeprod/mariadb-generic-aarch64#g
s#%%SECONDARY_HUB%%#betothreeprod/mariadb-generic-aarch64#g
