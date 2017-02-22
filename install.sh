#!/bin/bash

echo;
echo "Installing project";

composer install;
./bin/console doctrine:database:create;
./bin/console doctrine:schema:create;
./bin/console hautelook:fixtures:load -n;
npm install
bower install
gulp
