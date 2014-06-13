#!/bin/sh
echo "Starting new project build"
echo "Please ensure that the MySQL Connection to the given parameters is available!"
while true; do
    echo .
    read -p "This will completely recreate the TIXI project database! Confirm with y(es) or n(o):  " yn
    case $yn in
        [Yy]* ) break;;
        [Nn]* ) exit;;
        * ) echo "Please answer yes or no.";;
    esac
done

echo .
echo "Starting Composer dependencies"
php composer.phar install
php composer.phar update

echo .
echo "Recreate database"
php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force
php app/console project:build-fulltext

echo .
echo "Insert test_data"
cd build
php insert_mysqldata.php test_data.sql