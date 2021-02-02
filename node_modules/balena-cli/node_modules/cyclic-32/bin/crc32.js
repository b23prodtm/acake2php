#!/usr/bin/env node
var crc32 = require( '../lib/crc32' )

var filename = process.argv.slice(2).shift()
var readStream = filename ?
  require( 'fs' ).createReadStream( filename ) :
  process.stdin

readStream
  .pipe( new crc32.Hash({ encoding: 'hex' }) )
  .once( 'end', () => process.stdout.write( '\n' ) )
  .pipe( process.stdout )
