#!/bin/bash

echo;
echo "what you wanna do?"
echo "1 - install project"
echo "2 - cache clear"
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
      ./bin/console c:c
   ;;

   "0" ) ;;
esac