<?php
/**
 * Created by PhpStorm.
 * User: jonasse
 * Date: 10.01.14
 * Time: 08:58
 */

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class DataMiner extends Controller
{/*
  * service for providing generic access to the data stored in the MySQL Innodb database
  * also includes date conversion functions:
  *     user-forms = swiss german date (dd.mm.yyyy)
  *     database   = ISO format (yyyy-mm-dd)
  */
    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    /* --------------- validate swiss dates / times --------------- */

    public function validDate($value)
    {/* input dd.mm.yyyy (de-CH locale)
      * validate the date (without using UNIX datetime)
      * output: true is a valide date
      *         false is invalid
      */
        $arr = explode(".", $value);
        if (count($arr) == 3) {
            if (ctype_digit($arr[0]) and
                ctype_digit($arr[1]) and
                ctype_digit($arr[2]) and
                ($arr[2]>1000))
            {
                return checkdate($arr[1], $arr[0], $arr[2]);
            }
        }
        return false;
    }

    public function validTime($value)
    {/* input hh:mm
      * validate the time format
      */
        $valid = false;
        $tim = explode(":", $value);
        if (count($tim) == 2)
        {
            $valid = (($tim[0] >= 0 && $tim[0] <= 23) and
                ($tim[1] >= 0 && $tim[1] <= 59));
        }
        return $valid;
    }

    public function validDateTime($value)
    {/* input dd.mm.yyyy hh:mm (de-CH locale)
      * validate the datetime (without using UNIX datetime)
      * output: true is a valide date time
      *         false is invalid
      */
        $arr = explode(" ", $value);
        if (count($arr)==2)
        {
            return (($this->validDate($arr[0])) and ($this->validTime($arr[1])));
        }
        return false;
    }

    /* --------------- localize = convert ISO 8601 date / time to swiss dates / times   --------------- */
    /* --------------- delocalize = convert swiss date / time to ISO 8601 dates / times --------------- */

    public function localizeDate($value)
    {/*
      * convert the MySQL compliant (ISO 8601) date strings into de-CH localized date strings
      * like: convert the date string from yyyy-mm-dd to dd.mm.yyyy
      * input empty (null) outputs dd.mm.yyy (today)
      * input error outputs 00.00.0000
      */
        if ((is_null($value)) or ($value == "0000-00-00")) {
            $dd = date('d');
            $mm = date('m');
            $yy = date('Y');
        } else {
            $dt = explode('-', $value);
            if (count($dt) == 3){
                $dd = trim($dt[2]);
                $mm = trim($dt[1]);
                $yy = trim($dt[0]);
            } else {
                $dd = '00';
                $mm = '00';
                $yy = '0000';
            }
        }
        $swiss = "$dd.$mm.$yy";
        return $swiss;
    }

    public function delocalizeDate($value)
    {/*
      * convert the de-CH localized date strings into MySQL compliant date strings (ISO 8601)
      * like: convert the date string from dd.mm.yyyy to yyyy-mm-dd
      * input empty (null) outputs yyyy-mm-dd (today)
      * input error outputs 0000-00-00
      */
        if (is_null($value)) {
            $dd = date('d');
            $mm = date('m');
            $yy = date('Y');
        } else {
            $dt = explode('.', $value);
            if (count($dt) == 3){
                $dd = trim($dt[0]);
                $mm = trim($dt[1]);
                $yy = trim($dt[2]);
            } else {
                $dd = '00';
                $mm = '00';
                $yy = '0000';
            }
        }
        $mysql = "$yy-$mm-$dd";
        return $mysql;
    }

    public function localizeTime($value)
    {/*
      * convert the MySQL compliant (ISO 8601) time strings into de-CH localized time strings
      * like: convert the time string from hh:mm:ss to hh:mm
      * input empty (null) outputs hh:mm (now)
      * input error outputs 00:00
      */
        if (is_null($value)) {
            $hh = date('H');
            $mm = date('i');
        } else {
            $dt = explode(':', $value);
            if (count($dt) == 3){
                $hh = trim($dt[0]);
                $mm = trim($dt[1]);
            } else {
                $hh = '00';
                $mm = '00';
            }
        }
        $swiss = "$hh:$mm";
        return $swiss;
    }

    public function delocalizeTime($value)
    {/*
      * convert the de-CH localized time strings into MySQL time strings (ISO 8601)
      * like: convert the time string from hh:mm to hh:mm:00
      * input empty (null) outputs hh:mm (now)
      * input error outputs 00:00:00
      */
        if (is_null($value)) {
            $hh = date('H');
            $mm = date('i');
        } else {
            $dt = explode(':', $value);
            if (count($dt) == 2){
                $hh = trim($dt[0]);
                $mm = trim($dt[1]);
            } else {
                $hh = '00';
                $mm = '00';
            }
        }
        $mysql = "$hh:$mm:00";
        return $mysql;
    }

    public function localizeDateTime($value)
    {/*
      * convert the MySQL compliant (ISO 8601) date strings into de-CH localized date strings
      * like: convert the datetime string from yyyy-mm-dd hh:mm:ss to dd.mm.yyyy hh:mm
      * input empty (null) outputs dd.mm.yyyy hh:mm (today)
      * input error outputs 00.00.0000 00:00
      */
        if (is_null($value)) {
            $swiss = date('d.m.Y H:i');
        } else {
            $dt = explode(' ', $value);
            if (count($dt) == 2){
                $da = $this->localizeDate($dt[0]);
                $ti = $this->localizeTime($dt[1]);
                $swiss = "$da $ti";
            } else {
                $swiss = '00.00.0000 00:00';
            }
        }
        return $swiss;
    }

    public function delocalizeDateTime($value)
    {/*
      * convert the de-CH localized datetime strings into MySQL compliant datetime strings (ISO 8601)
      * like: convert the local datetime string from dd.mm.yyyy hh:mm to yyyy-mm-dd hh:mm:00
      * input empty (null) outputs yyyy-mm-dd hh:mm:ss (today)
      * input error outputs 0000-00-00 00:00:00
      */
        if (is_null($value)) {
            $mysql = date('Y-m-d H:i:s');
        } else {
            $dt = explode(' ', $value);
            if (count($dt) == 2){
                $da = $this->delocalizeDate($dt[0]);
                $ti = $this->delocalizeTime($dt[1]);
                $mysql = "$da $ti";
            } else {
                $mysql = '0000-00-00 00:00:00';
            }
        }
        return $mysql;
    }

    /* --------------- common functions for managing shifts ----------------- */

    public function getShiftName($id)
    {/* convert shift id to shift name */
        $session = $this->container->get('session');
        $shifts = $session->get('shifts');
        foreach ($shifts as $values)
        {
            if ($values['dienst_id'] == $id)
            {
                return $values['dienst_name'];
            }
        }
        $session->set('errormsg', "Validierungsfehler: ungültige Dienst ID ($id).");
        return $shifts[0]['dienst_name']; // return the first valid name
    }

    public function getShiftId($name)
    {/* convert shift name to shift id */
        $session = $this->container->get('session');
        $shifts = $session->get('shifts');
        foreach ($shifts as $values)
        {
            if ($values['dienst_name'] == $name)
            {
                return $values['dienst_id'];
            }
        }
        $session->set('errormsg', "Validierungsfehler: ungültige Dienst Name ($name).");
        return $shifts[0]['dienst_id']; // return the first valid id
    }

    public function makeShifts()
    {/*
      * add shifts array and html (for each shift in the database) to session
      * <input type="radio" name ="dienst?" value="Schicht 1" title="09:00 - 13:00" checked >Schicht 1
      */
        $session = $this->container->get('session');
        $customer = $session->get('customer');
        $shifts = $this->readData("select * from $customer.list_dienst");
        if (count($shifts) >0)
        {   $session->set('shifts', $shifts); // shifts as an array
            $str = "\n";
            foreach ($shifts as $key => $values)
            {
                $str .= "<input type=\"radio\" name =\"dienst?\" value=\"".$values['dienst_name']."\" ";
                $str .= "title=\"".substr($values['dienst_anfang'],0,5)." - ".substr($values['dienst_ende'],0,5)."\"";
                if ($key == 0){
                    $str .= " checked";
                }
                $str .= " >".$values['dienst_name']."\n";
            }
            $session->set('shiftsHTML', $str); // shifts as HTML (template)
            return true; // success
        }
        return false; // failure
    }

    /* --------------- generic MySQL database access functions --------------- */

    public function readData($sql)
    {/*
      * read data from the table or views defined in the $sql statement
      *   if applicable, errors are stored in session->errormsq
      *   return an array of data
      */
        try
        {/* initialize variables */
            $connection = $this->container->get('database_connection');
            return $connection->fetchAll( $sql );
        }
        catch (PDOException $e)
        {
            $session = new Session;
            $session->set("errormsg","Cannot access database : ".$e);
            return array(); // empty
        }
    }

    public function execData($sql)
    {/*
      * delete / update data in the table or views defined in the $sql statement
      *   if applicable, errors are stored in session->errormsq
      *   use this only if you can trust the data in the sql statement
      *   return success (true) failure (false)
      */
        try
        {
            $connection = $this->container->get('database_connection');
            $connection->beginTransaction();
            $connection->exec($sql);
            $connection->commit();
            return true; // success
        }
        catch (PDOException $e)
        {
            $session = new Session;
            $session->set("errormsg","Cannot access database : ".$e);
            $connection->rollback();
            return false; // failure
        }
    }

    public function insertData($myarray, $mytable)
    {/*
      * insert the data contained in $myarray in to table $mytable
      *   the $myarray keys have the same names as the table columns
      *   if applicable, errors are stored in session->errormsq
      *   return success (true) failure (false)
      * syntax: "INSERT IGNORE INTO $mytable (fields) VALUES (values)";
      */
        $prefix = "INSERT IGNORE INTO $mytable (";
        try
        {
            $connection = $this->container->get('database_connection');
            $connection->beginTransaction();
            foreach ($myarray as $values)
            {
                $f = "";
                $v = "";
                foreach ($values as $key => $value) {
                    $f .= $key.", ";
                    $v .= $value.", ";
                }
                $sql = $prefix.substr($f, 0, -2).") VALUES (".substr($v, 0, -2).")";
                $connection->exec($sql);
            }
            $connection->commit();
            return true; // success
        }
        catch (PDOException $e)
        {
            $session = new Session;
            $session->set("errormsg","Cannot access database : ".$e);
            $connection->rollback();
            return false; // failure
        }
    }
}