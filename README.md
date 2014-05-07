iTixi
=====

Symfony2-iTixi master repository

This is the working repository for the iTixi project at
    http://sourceforge.net/projects/itixi/

Please view this project for more details. Thank you.


1) Minimal Requirements
-----------------------

PHP Version 5.5

MySQL Version 5.6

Doctrine 2.4


### Configuration Requirements
PHP Extensions must be enabled and configured (mostly in php.ini):

`php_mysqli`

`xdebug`

`php_openssl`   (probably to download via composer)


PHP Parameters shall be set to:

`xdebug.max_nesting_level=200`  200 or more

`date.timezone`     must be set to a valid Timezone


2) Build and Deploy
--------------------

### Build iTixi
Execute `run_build.sh` to run composer install for dependencies and
doctrines database mapping, asset installation and test_data import.
(For Windows you will need a Bash Console, for example install msysgit Git for Windows with bash and .sh link)
For test_data import set the mysql_connection parameters in `build\mysql_connection.php`.
Composer install (executed in the run_build.sh) will ask on the first time run for the Database connection and Timezone.

If `run_build.sh` doesn't work properly, make shure you can run the following command manually:

`php composer.phar install`

`php app/console doctrine:database:create`

`php app/console doctrine:schema:update --force`

`php app/console project:build-fulltext`

The last command updates a fulltext index, which is only possible in MySQL Version 5.6 and higher.


3) Test Users
-------------

Users for login tests with different ROLES are

#### ROLE_ADMIN
User:       admin
Password:   pass

#### ROLE_MANAGER
User:       manager
Password:   pass

#### ROLE_DISPO
User:       dispo
Password:   pass
