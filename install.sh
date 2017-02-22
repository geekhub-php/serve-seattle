#!/bin/bash

echo;
echo "Installing project";

composer install;
./bin/console d:d:c;
./bin/console d:s:c;
./bin/console h:f:l -n;
npm install
bower install --allow-root
gulp
