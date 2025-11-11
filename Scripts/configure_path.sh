#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
mkdir -p "$TOPDIR/app/tmp/cache/long"
mkdir -p "$TOPDIR/app/tmp/cache/persistent"
mkdir -p "$TOPDIR/app/tmp/cache/models"
mkdir -p "$TOPDIR/app/tmp/tests"
mkdir -p "$TOPDIR/app/tmp/logs"
mkdir -p "$TOPDIR/app/logs"
mkdir -p "$TOPDIR/log"
chmod -Rv 1770 "$TOPDIR/app/tmp"
chmod -Rv 1770 "$TOPDIR/app/logs"
chmod -Rv 1770 "$TOPDIR/log"
