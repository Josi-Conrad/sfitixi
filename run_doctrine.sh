echo "Recreate database"
php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force