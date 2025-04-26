s/%%BALENA_MACHINE_NAME%%//g
s/(Dockerfile\.)[^\.]*/\1armhf/g
s/%%BALENA_ARCH%%/armhf/g
s/(BALENA_ARCH[=:-]+)[^$ }]+/\1armhf/g
