# @balena/es-version

This module allows specifying a target es version for other modules to require, currently supported target versions:
* es3 (default)
* es5
* es2015
* es2016
* es2017
* es2018
* es2019
* es2020
* esnext

## Usage as a client

1. Add @balena/es-version to your package.json with a  `^` version specifier, eg
```json
"@balena/es-version": "^1.0.0",
```
Note: If using locked dependences, eg package-lock.json/npm-shrinkwrap.json/etc, then make sure to also dedupe the module so that the same instance of the module is used everywhere.

2. Within your module require it before anything else and set the desired version:
```js
// Must be set before the first require of an @balena/es-version supporting module
require('@balena/es-version').set('es5');
```
and then the closest matching version will be used by any module that supports choosing the target version, with the priority being:
1. exact match
2. closest match under the desired version
3. lowest supported version

## Usage as a module provider

1. Add @balena/es-version to your package.json with a  `^` version specifier, eg
```json
"@balena/es-version": "^1.0.0",
```

2. Generate builds for the es versions you wish to support along with types for the shim you'll create, eg
```json
"build-es5": "tsc --target es5 --outDir es5",
"build-es2015": "tsc --target es2015 --outDir es2015",
"build-es2018": "tsc --target es2018 --outDir es2018",
"build-types": "tsc --emitDeclarationOnly --outDir .",
"build": "npm run build-es5 && npm run build-es2015 && npm run build-es2018 && npm run build-types",
```

2. Create a shim to use as your entry-point which loads the desired version, eg
```js
// First get the es version to use:
var esVersion = require('@balena/es-version').get(['es5', 'es2015', 'es2018');
// Then include/return the correct version of your module, eg
module.exports = module.exports = require('./' + esVersion);
```
Note: This must be compatible with your lowest supported es version

3. Point your package.json to the new shim and types, eg
```json
"main": "index.js",
"types": "index.d.ts",
```

4. It is also recommended to add a "browser" entry to a fixed es version in your package.json in order to support browser bundlers with default config, eg
```json
"browser": "es5"
```
