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
Change parameters.yml to specify your database connection

Execute `run_build.sh` to run composer install for dependencies and
doctrines database mapping, asset installation and test_data import.
For test_data import set the mysql_connection parameters in `build\mysql_connection.php`


[1]:  http://symfony.com/doc/2.3/book/installation.html