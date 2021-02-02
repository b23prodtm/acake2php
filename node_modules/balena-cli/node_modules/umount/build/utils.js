var os;

os = require('os');


/**
 * @summary Check if operating system is Windows
 * @private
 * @function
 *
 * @returns {Boolean} whether the os is Windows
 *
 * @example
 * utils.isWin32()
 */

exports.isWin32 = function() {
  return os.platform() === 'win32';
};


/**
 * @summary Check if operating system is OS X
 * @private
 * @function
 *
 * @returns {Boolean} whether the os is OS X
 *
 * @example
 * utils.isMacOSX()
 */

exports.isMacOSX = function() {
  return os.platform() === 'darwin';
};


/**
 * @summary Check if operating system is Linux
 * @private
 * @function
 *
 * @returns {Boolean} whether the os is Linux
 *
 * @example
 * utils.isLinux()
 */

exports.isLinux = function() {
  return os.platform() === 'linux';
};
