#!/bin/bash

dirname=$(dirname "$0")
DATABASE=${1-http://localhost:5984/traduxio}

echo Filling in $DATABASE
for i in "$dirname"/samples/*.json; do
  echo Loading $i into $DATABASE
  if ! curl -m 10 -s -XPOST -H "Content-Type: application/json" -d@$i $DATABASE -o /dev/null; then
    echo Failed; exit 1;
  fi
done
