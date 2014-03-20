echo "Starting Composer dependencies"
php composer.phar install
php composer.phar update

echo .
echo "Recreate database"
php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force

echo .
echo "Installing Assets with symlinks"
php app/console assets:install --symlink

echo .
echo "This will insert data to you mysql server."
echo "Please set connection infos in install/mysql_connection.php for current connection"
cd build
php insert_basedata.php
php insert_userdata.php
php insert_vehicledummydata.php