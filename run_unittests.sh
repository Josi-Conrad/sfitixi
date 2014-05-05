#!/bin/sh
echo "Running PHPUnit Tests"
php phpunit.phar -c app/phpunit.xml --testdox-html docs/PHPUnit-Tests.html --log-tap docs/PHPUnit.log