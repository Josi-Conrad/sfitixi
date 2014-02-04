@echo off
SET /P YNvar=Datenbank itixi, btb wiederherstellen?(Ja oder Nein):
if "%YNVar%"=="J" goto doYES
if "%YNVar%"=="j" goto doYES
if "%YNVar%"=="Ja" goto doYES
if "%YNVar%"=="ja" goto doYES
goto doNO
:doYES
echo Datenbank wiederherstellen ...
mysql --host=127.0.0.1 -u root -p -e "CREATE DATABASE IF NOT EXISTS itixi; SHOW DATABASES LIKE 'itixi'"
mysql --host=127.0.0.1 -u root -p -v btb < itixi_backup.sql
pause
mysql --host=127.0.0.1 -u root -p -e "CREATE DATABASE IF NOT EXISTS btb; SHOW DATABASES LIKE 'btb'"
mysql --host=127.0.0.1 -u root -p -v btb < btb_backup.sql
goto done
:doNO
echo Datenbank wird nicht wiederhergestellt
:done
pause
