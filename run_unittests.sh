#!/bin/sh
echo "Running PHPUnit Tests"
php phpunit.phar -c app --testdox-html docs/PHPUnit-Tests.html --log-tap docs/PHPUnit.log --coverage-html docs/PHPUnit-Coverage/