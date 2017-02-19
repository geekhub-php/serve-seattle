#!/bin/bash

echo;
echo "what you wanna do?"
echo "1 - install project"
echo "2 - install front"
echo "3 - cache clear"
echo "0 - exit"
echo;

read key

case "$key" in
   "1" )
      composer install
      ./bin/console d:s:u -f
      ./bin/console doctrine:fixtures:load
   ;;

   "2" )
      npm install bower
      npm install gulp
      npm install gulp-less
      npm install gulp-clean
      npm install gulp-uglify
      npm install gulp-concat
      bower install
      gulp
   ;;

   "3" )
      ./bin/console c:c
    ;;

   "0" ) ;;
esac