os = require('os')

###*
# @summary Check if operating system is Windows
# @private
# @function
#
# @returns {Boolean} whether the os is Windows
#
# @example
# utils.isWin32()
###
exports.isWin32 = ->
	return os.platform() is 'win32'

###*
# @summary Check if operating system is OS X
# @private
# @function
#
# @returns {Boolean} whether the os is OS X
#
# @example
# utils.isMacOSX()
###
exports.isMacOSX = ->
	return os.platform() is 'darwin'

###*
# @summary Check if operating system is Linux
# @private
# @function
#
# @returns {Boolean} whether the os is Linux
#
# @example
# utils.isLinux()
###
exports.isLinux = ->
	return os.platform() is 'linux'
