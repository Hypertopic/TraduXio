#!/bin/bash

DATABASE=${1-http://localhost:5984/traduxio}

cd $(dirname "$0")
for i in samples/*.json; do
  curl -XPOST -H "Content-Type: application/json" -d@$i $DATABASE
done
