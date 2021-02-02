"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.extractInlineWebpack = extractInlineWebpack;

// Not fond of default exports, disable here */

/* eslint import/prefer-default-export: off */
function extractInlineWebpack(value) {
  const parts = value.split('!');

  if (parts.length === 1) {
    return value;
  }

  return parts.pop();
}