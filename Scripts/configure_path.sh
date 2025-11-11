#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
mkdir -p "$TOPDIR/app/tmp/cache/long"
mkdir -p "$TOPDIR/app/tmp/cache/persistent"
mkdir -p "$TOPDIR/app/tmp/cache/models"
mkdir -p "$TOPDIR/app/tmp/tests"
mkdir -p "$TOPDIR/app/tmp/logs"
mkdir -p "$TOPDIR/log"
chmod -Rv 1776 "$TOPDIR/app/tmp"
ln -vs "$TOPDIR/config" "$TOPDIR/app/Config"
ln -vs "$HTDOCS" "${HTDOCS}-https"
