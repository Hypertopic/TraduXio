#!/bin/bash

dirname=$(dirname "$0")

DATABASE=${1-http://localhost:5984/traduxio}

curl -s -X DELETE $DATABASE -o /dev/null
"$dirname"/../deploy.sh $DATABASE
"$dirname"/load_data.sh $DATABASE
