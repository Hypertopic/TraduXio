#!/bin/bash

DATABASE=${1-http://localhost:5984/traduxio}

curl -s -X DELETE $DATABASE -o /dev/null
couchapp push couchdb $DATABASE 2> /dev/null
$(dirname $0)/load_data.sh $DATABASE
