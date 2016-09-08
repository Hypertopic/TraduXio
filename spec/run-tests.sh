#!/bin/bash
dirname=$(dirname "$0")

rspec $dirname/features/*.rb
