#!/bin/bash

echo;
echo "Installing project";

composer install;
./bin/console doctrine:database:create;
./bin/console doctrine:schema:create;
./bin/console hautelook:fixtures:load -n;

./bin/console doctrine:database:create --env=test;
./bin/console doctrine:schema:create --env=test;
./bin/console hautelook:fixtures:load -n --env=test;

npm install
bower install
gulp
