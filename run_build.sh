echo "Starting new project build"
echo .
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

echo .
echo "Installing Assets with symlinks"
php app/console assets:install --symlink

echo .
echo "This will insert data to you mysql server."
echo "Please set connection infos in install/mysql_connection.php for current connection"
cd build
php insert_mysqldata.php categories.sql
php insert_mysqldata.php users.sql
php insert_mysqldata.php vehicle_dummy.sql
php insert_mysqldata.php addresses.sql