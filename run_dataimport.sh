cd build

echo .
echo "This will insert data to you mysql server."
echo "Please set connection infos in build/mysql_connection.php for current connection"

php insert_mysqldata.php address_alter.sql

php insert_mysqldata.php categories.sql
php insert_mysqldata.php addresses.sql
php insert_mysqldata.php users.sql
php insert_mysqldata.php vehicle_dummy.sql
php insert_mysqldata.php persons.sql