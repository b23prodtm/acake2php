umount
------

[![npm version](https://badge.fury.io/js/umount.svg)](http://badge.fury.io/js/umount)
[![dependencies](https://david-dm.org/jviotti/node-umount.png)](https://david-dm.org/jviotti/node-umount.png)
[![Build Status](https://travis-ci.org/jviotti/node-umount.svg?branch=master)](https://travis-ci.org/jviotti/node-umount)
[![Build status](https://ci.appveyor.com/api/projects/status/q90qlg6w6x3ifte1?svg=true)](https://ci.appveyor.com/project/jviotti/node-umount)

Unmount a device in UNIX, do nothing in Windows.

This module doesn't include native bindings, it constructs and runs the
corresponding `umount` command with [child_process.exec()](https://nodejs.org/api/child_process.html#child_process_child_process_exec_command_options_callback).

If the platform is `win32`, the `umount` function does nothing, and calls the given callback with `(null, null, null)`. This allows the client to call this module independently of the operating system while getting the desired behaviour.

Installation
------------

Install `umount` by running:

```sh
$ npm install --save umount
```

Documentation
-------------

### umount.umount(String device, Function callback)

Unmount a device.

The callback gets three arguments: `(error, stdout, stderr)`.

Example:

```coffee
umount = require('umount') 

umount.umount '/dev/disk2', (error, stdout, stderr) ->
	throw error if error?
	console.log(stdout)
```

### umount.isMounted(String device, Function callback)

Check if a device is mounted.

**Note:** This function always yields `true` in `win32`.

The callback gets two arguments: `(error, isMounted)`.

Example:

```coffee
umount = require('umount') 

umount.isMounted '/dev/disk2', (error, isMounted) ->
	throw error if error?
	console.log("Is Mounted? #{isMounted}")
```

Tests
-----

Run the test suite by doing:

```sh
$ gulp test
```

Contribute
----------

- Issue Tracker: [github.com/jviotti/node-umount/issues](https://github.com/jviotti/node-umount/issues)
- Source Code: [github.com/jviotti/node-umount](https://github.com/jviotti/node-umount)

Before submitting a PR, please make sure that you include tests, and that [coffeelint](http://www.coffeelint.org/) runs without any warning:

```sh
$ gulp lint
```

Support
-------

If you're having any problem, please [raise an issue](https://github.com/jviotti/node-umount/issues/new) on GitHub.

ChangeLog
---------

### v1.1.6

- Update outdated dependencies.

### v1.1.5

- Escape device paths containing spaces.

### v1.1.4

- Pass `force` to `diskutil unmountDisk` in OS X.

### v1.1.3

- Call `/usr/sbin/diskutil` instead of just `diskutil` in OS X.

### v1.1.2

- Do not use `sudo` to unmount disks in OS X.

### v1.1.1

- Redirect Linux `umount` stderr output to /dev/null and ignore return code.

### v1.1.0

- Implement umount.isMounted() function.

### v1.0.1

- Fix improper unmounting of multiple partitions in GNU/Linux.

License
-------

The project is licensed under the MIT license.
