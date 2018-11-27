#!/bin/bash
set -e
if [[ "$#" < 3 ]]; then echo "Usage : ./Scripts/cp_bkp_old.sh <working-directory> <source-file> <target-file>"; exit 1; fi
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
echo -e "${src} copied. Please, review the files.\n"
