#!/bin/bash

DATABASE=${1-http://localhost:5984/traduxio}

echo Deploying on $DATABASE
if ! couchapp push couchdb $DATABASE 2> /dev/null; then echo Failed; exit 1; fi
