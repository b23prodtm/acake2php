#!/bin/bash
set -e
wd=$1
src=$2
dst=$3
pwd=`pwd`
cd $wd
if [[ ( -f $dst ) && ( -f $src ) && ( $(which md5) > /dev/null ) ]]; then
# read or operation to define $file1 & $file2 here ...
  val1=`md5 -q $src`
  val2=`md5 -q $dst`
  tmpval="Z${val1}" ; val1="${tmpval}"
  tmpval="Z${val2}" ; val2="${tmpval}"
  if [ $val1 != $val2 ]; then
    # files are not the same, do operation here ..
    cp -v $dst "${dst}.old"
  fi
fi
cp -v $src $dst
cd $pwd
echo -e "Okay, just run the shell script. Please, review the files.\n"
