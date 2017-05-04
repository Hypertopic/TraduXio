#!/bin/bash
dirname=$(dirname "$0")
if [ -n "$1" -a "$1" == "${1#-}" ]; then #$1 doesn't start with a '-'
  echo setting DATABASE to $1
  DATABASE=$1
  shift
else
  DATABASE="http://localhost:5984/traduxio"
fi

TEST_URL="$DATABASE/_design/traduxio/_rewrite/" rspec -P $dirname/features/*.rb $@
