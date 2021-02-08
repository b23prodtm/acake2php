#!/usr/bin/env bash
TOPDIR=$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)
mkdir -p "$TOPDIR/app/tmp/cache/persistent"
mkdir -p "$TOPDIR/app/tmp/cache/models"
mkdir -p "$TOPDIR/app/tmp/tests"
mkdir -p "$TOPDIR/app/tmp/logs"
touch "$TOPDIR/app/tmp/logs/error.log"
chmod -Rv 1775 "$TOPDIR/app/tmp"
