cd build

echo .
echo "This will insert data to you mysql server."
echo "Please set connection infos in install/mysql_connection.php for current connection"

while true; do
    echo .
    read -p "Insert base data (categories...)? (y or no): " yn
    case $yn in
        [Yy]* ) php insert_basedata.php; break;;
        [Nn]* ) break;;
        * ) echo "Please answer yes or no.";;
    esac
done

while true; do
    echo .
    echo .
    read -p "Insert user data (roles, user and passwords)? (y or no): " yn
    case $yn in
        [Yy]* ) php insert_userdata.php; break;;
        [Nn]* ) break;;
        * ) echo "Please answer yes or no.";;
    esac
done

while true; do
    echo .
    echo .
    read -p "Insert vehicledummy data (roles, user and passwords)? (y or no): " yn
    case $yn in
        [Yy]* ) php insert_vehicledummydata.php; break;;
        [Nn]* ) break;;
        * ) echo "Please answer yes or no.";;
    esac
done

