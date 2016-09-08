#!/bin/bash
dirname=$(dirname "$0")
DATABASE=${1-http://localhost:5984/traduxio}

TEST_URL="$DATABASE/_design/traduxio/_rewrite/" rspec $dirname/features/*.rb
