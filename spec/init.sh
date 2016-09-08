#!/bin/bash

dirname=$(dirname "$0")

DATABASE=${1-http://localhost:5984/traduxio}

echo Deleting $DATABASE
curl -m 10 -s -X DELETE $DATABASE -o /dev/null
echo $DATABASE deleted
if ! "$dirname"/../deploy.sh $DATABASE; then exit 1; fi
if ! "$dirname"/load_data.sh $DATABASE; then exit 1; fi
