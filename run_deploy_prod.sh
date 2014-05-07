#!/bin/sh
echo "Deploy prod"
rm app/cache/* app/logs/* -f -R
chmod 777 -R app/cache app/logs
php app/console cache:clear --env=prod --no-debug
php app/console assetic:dump --env=prod --no-debug
