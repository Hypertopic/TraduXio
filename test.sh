#!/bin/bash
dirname=$(dirname "$0")
DATABASE=${1-}

"$dirname"/spec/init.sh $DATABASE && "$dirname"/spec/run-tests.sh $DATABASE
