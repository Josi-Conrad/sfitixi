iTIXI
=====

Symfony2-iTixi master repository

This is the working repository for the iTixi project at
    http://sourceforge.net/projects/itixi/

Please view this project for more details. Thank you.


1) Build and Deploy
--------------------

### Symfony 2.3 Installation
See [**Symfony Install Book**][1] for detailed instructions

### Build iTixi
Execute `run_build.sh` to run composer install for dependencies and
doctrines database mapping, asset installation and test_data import.
For test_data import set the mysql_connection parameters in `build\mysql_connection.php`.
Composer install (executed in the run_build.sh) will ask on the first time run for the Database connection and TimeZone.

If `run_build.sh` doesn't work properly, make shure you can run the following command manually:
`php composer.phar install`

`php app/console doctrine:database:create`

`php app/console doctrine:schema:update --force`

And run_dataimport.sh for Testdata, which imports the .SQL files in the /build folder.


2) Test Users
-------------

Users for login tests with different ROLES are

### ROLE_ADMIN
User:       admin
Password:   pass

### ROLE_MANAGER
User:       manager
Password:   pass

### ROLE_USER
User:       user
Password:   pass

[1]:  http://symfony.com/doc/2.3/book/installation.html