<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 30.09.13
 * Time: 12:15
 */
// 23.10.2013 martin jonasse finished basic form functions add, modify, save, delete, and quit
// 28.10.2013 martin jonasse fixed some features
// 04.11.2013 martin jonasse upgrade cursor to structured namespace, split off $mydata from $myform
// 02.12.2013 martin jonasse added time format

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class FormStateBuilder extends Controller
{
    protected $container;       // container
    protected $session;         // session
    protected $conn;            // database connection

    protected $callback;        // input: callback function for validating the data in $myform
    protected $formview;        // input (name of the MySQL form view)
    protected $listview;        // input (name of the MySQL list view)
    protected $pkey;            // input (name of the primary key)
    protected $collection;      // true: collection of objects (many), false: one object
    protected $constraint = ""; // input: where foo = 'bar' (resolves to one record)

    protected $constraint_key;  // input: derived from $constraint
    protected $constraint_id;   // input: derived from $constraint

    protected $myform;          // meta plus values for rendering and validating a form
    protected $mytabs;          // meta data of the tables used in the formview

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setCallback($value)
    {/*
      * define the name of the callback function, default = empty
      */
        $this->callback = $value;
    }

    public function setFormView($value)
    {/*
      * define the name of the view
      * the mysql database defines the data structure and constraints
      * please observe MySQL chapter 18.4.3 Updateable amd Insertable Views
      */
        $this->formview = $value;
        $this->session = new session;
        $this->conn = $this->get('database_connection');
        $this->mytabs = array();
    }

    public function setListView($value)
    {/*
      * define the name of the view
      * the mysql database defines the data structure and constraints
      * please observe MySQL chapter 18.4.3 Updateable amd Insertable Views
      */
        $this->listview = $value;
    }


    public function setPkey($value)
    {/*
      * define the name of the primary key for the $view
      */
        $this->pkey = $value;
    }

    public function setCollection($value)
    {/*
      * true: collection of objects (many), false: one object
      */
        $this->collection = $value;
    }

    public function setConstraint($value="")
    {/*
      * define the constraint for the $view (where foo = 'bar')
      * use case 1: benutzername = martin@btb.ch
      *             (one of many relationship)
      * use case 2: fahrzeug_fk = 13
      *             (one to many relationship)
      */
        if ($value != "") {
            $this->constraint = $value;
            $temp = explode("=", $value);
            if (count($temp) == 2) {
                $this->constraint_key = trim($temp[0]); // always a column name
                $this->constraint_id = trim($temp[1]); // can be a text or a number
            } else {
                $this->constraint_key = null;
                $this->constraint_id = null;
                $this->session->set('errormsg', "Fehler in constraint (SQL expression?): $value.");
            }
        }
    }

    private function getLen($mysqltype)
    {/*
      * find the length of a sql type, e.g. varchar(10) = 10
      */
        $loidx = strpos($mysqltype, "(");
        $hiidx = strpos($mysqltype, ")");
        if (($loidx !== false) and ($hiidx !== false)) {
            $len = substr($mysqltype, $loidx+1, $hiidx-$loidx-1);
        } else {
            $len = '0';
        }
        return $len;
    }

    private function map2InputType($sqltype, $fieldname)
    {/*
      * map  each MySQL type to the corresponding html input tag type, or
      * to a jQuery class (date / time elements rendered as text)
      * example: <input type="text" name="Vorname">
      */
        switch ($sqltype)
        {
            case 'int':         return 'number';
            case 'varchar':     return 'text';
            case 'tinyint':     return 'checkbox';
            case 'text':        return 'textarea';
            case 'mediumtext':  return 'textarea';
            case 'time':        return 'jq_timepicker';
            case 'datetime':    return 'jq_datetimepicker';
            case 'date':
                if (($fieldname == 'von') or ($fieldname == 'bis'))
                              { return 'jq_daterangepicker'; }
                elseif (strpos($fieldname, '_geburtstag')!==false)
                              { return 'jq_birthdaypicker'; }
                else          { return 'jq_datepicker'; }
            default:            return 'undefined';
        }
    }

    private function setMetaData($route)
    {/*
      * retrieve the meta data for the table(s) from the customer database or the session cache
      */
        if ($this->session->has("myform/$route"))
        {/* get metadata from session cache */
            $this->myform = $this->session->get("myform/$route");
            return true; // successfully set data in $this->myform
        }

        try {
            // make a database call to get the meta data
            $customer = $this->session->get('customer');
            $sql = "show full columns from $customer.$this->formview";
            $this->myform = $this->conn->fetchAll( $sql );

            // upgrade the meta data with some derived fields
            foreach ($this->myform as $key => $value) {

                // readonly?
                $postfix = substr($value["Field"], -3 ); // last three characters
                $readonly = (($postfix == '_id') or ($value == 'pin')); // primary key and migration id
                $this->myform[$key]["Readonly"] = $readonly;

                // hidden? = all foreign keys and all ids except the primary key
                $hidden = (($postfix == '_fk') or
                          (($postfix == '_id') and ($value["Field"] != $this->pkey))); // exception = pkey
                $this->myform[$key]["Hidden"] = $hidden;

                // maxlength?
                $this->myform[$key]["Length"] = $this->getLen($value["Type"]);

                // types?
                $paranthesis = strpos($value["Type"], "(");
                $basetype = ($paranthesis !== false) ? substr($value["Type"], 0, $paranthesis ) : $value["Type"];
                $this->myform[$key]["Basetype"] = $basetype; // sql type
                $this->myform[$key]["Rendered"] = $this->map2InputType($basetype, $value["Field"]); // html type, name

                // error message, previous value
                $this->myform[$key]["Error"] = ""; // empty
                $this->myform[$key]["Value"] = ""; // empty
            }
            $this->session->set("myform/$route", $this->myform); // cache metadata
            return true; // successfully set data in $this->myform

        } catch (PDOException $e) {
            $this->session->set("errormsg","Cannot access database : ".$e);
            $this->session->remove("myform/$route"); // clear cached metadata
            return false; // error message
        }
    }

    public function getFormMetaData()
    {/*
      * get the forms meta data, based upon the data in the (joined) table(s)
      * the submitted user data will be persisted to the database
      */
        return $this->myform;
    }

    private function setSubject()
    {/* set the session subject variable
      * based upon the cached context/$route
      */
        $route = $this->session->get("route");
        $subject = MenuTree::getCell($route, "CAPTION");
        $parent = menutree::getCell($route, "PARENT");
        if ($parent != "") {
            $subject = $this->session->get("context/$parent")." - ".$subject;
        }
        $this->session->set('subject', $subject);
        return;
    }

    private function getFormData($cursor)
    {/*
      * return an array containing the persistent data values for the form
      * todo: consider postfixing LOCK IN SHARE MODE to SELECT statement
      */
        $customer = $this->session->get('customer');

        try {
            // make a database call to get the form data (one record)
            $sql = "select * from $customer.$this->formview where $this->pkey=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue( 1, "$cursor", Type::INTEGER);
            $stmt->execute();
            // return an array with persistent data (query has only one record)
            $arr = $stmt->fetch();
            $this->setSubject();
            return $arr;

        } catch (PDOException $e) {
            $this->session->set("errormsg","Error reading the database: ".$e);
            return array();
        }
    }

    private function setFormData($cursor)
    {/*
      * update the data, changed by the user, in the customer database
      */
        $customer = $this->session->get('customer');
        $this->conn->beginTransaction();
        try
        {
            $count = 0;
            foreach ($this->myform as $value) {
                if ($value["Change"]) {
                    // make a database call to update one changed data column
                    $sql = "UPDATE ".$customer.".".$this->formview." SET ".$value["Field"]." = ? WHERE ".$this->pkey." = ?";
                    $count += $this->conn->executeUpdate($sql, array($value["Value"], $cursor));
                }
            }
            // return an array with persistent data (query has only one record)
            $this->conn->commit();
            return $count;

        }
        catch (PDOException $e)
        {
            $this->session->set("errormsg","Error updating the database: ".$e);
            $this->conn->rollback();
            return 0;
        }
    }

    private function deleteFormData($cursor)
    {/*
      * delete a record from the database, not per view, but for each table in the view ...
      */
        $customer = $this->session->get('customer');

        /* get database record for the view, containing the table indices */
        $record = $this->getFormData($cursor);
        $idx = 0;
        foreach ($record as $name => $value) {
            $this->myform[$idx]["Value"] = $value; // copy column value to form object
            $idx += 1;
        }
        $this->conn->beginTransaction();

        /* now try to delete all the table records in the view */
        try
        {
            foreach($this->myform as $key => $values) {
                if (strpos($values["Field"], "_id") !== false) {
                    // this is a primary index
                    $table = explode("_", $values["Field"])[0];
                    $indexname = $values["Field"];
                    $indexvalu = $values["Value"];
                    $sql = "DELETE FROM $customer.$table WHERE $indexname = $indexvalu";
                    $this->conn->exec($sql);
                }
            }
            $this->conn->commit();
            return true; // success
        }
        catch (PDOException $e)
        {
            $this->session->set("errormsg","Error deleting record from database: ".$e);
            $this->conn->rollback();
            return false; // error
        }
    }

    private function getInsertStatement($mycustomer, $mytable, $myarray)
    {/*
      * make sql INSERT IGNORE string
      * input mycustomer, mytable: the customername and tablename
      * input myarray: fieldname => value pairs, one for each column of $mytable
      * return: INSERT IGNORE INTO $customer.$table ('field1', 'field2') VALUES (NULL, NULL )
      */
        $myfields = "";
        $myvalues = "";
        foreach ($myarray as $key => $value)
        {/* concatinate fieldnames and values in two (CSV) lists */
            $myfields .= $key.", ";
            $myvalues .= $value.", ";
        }
        $myfields = substr($myfields, 0, strlen($myfields)-2 );
        $myvalues = substr($myvalues, 0, strlen($myvalues)-2 );
        return "INSERT IGNORE INTO $mycustomer.$mytable ($myfields) VALUES ($myvalues)";
    }

    private function setForeignKey($fkey, $fkid, $myarray)
    {/*
      * set the foreign key ($fkey) to $fkid anywhere in $myarray
      * the fully qualified foreign key must be unique in the database (mysql constraint)
      * syntax:  source_destination_fk
      * where:   source is the name of the table where the foreign key is stored
      *          destination is the name of the table where the foreign key points to
      * postfix: _fk
      * return: number of keys set or 0 (none)
      */
        $cntr = 0; // initial counter value
        foreach ($myarray as $tab => $value)
        {/* loop over local copy */
            $fqfk = $tab."_".$fkey; // fully qualified foreign key
            if (array_key_exists($fqfk, $value)) {
                if ($value[$fqfk] = "NULL")
                {/* set record number */
                    $this->mytabs[$tab][$fqfk] = $fkid;
                    $cntr += 1; // increment counter
                }
            }
        }
        return $cntr; // number of foreign keys changed from NULL to an integer value
    }

    private function insertEmptyRecord($mycustomer, $mytable, $myarray)
    {/*
      * insert an empty record in a table, where foreign keys are set properly
      * input mytable: $customer.$tablename, fully qualified tablename
      * input myarray: fieldname => value pairs, one for each column of $mytable
      * return: record id or 0 (error)
      */
        $this->conn->beginTransaction();
        try {
            $sql = $this->getInsertStatement($mycustomer, $mytable, $myarray);
            $this->conn->exec($sql); // insert empty record
            $id = $this->conn->lastInsertId(); // get record id
            $this->conn->commit();
            return $id;
        }
        catch (PDOException $e) {
            $this->session->set("errormsg","Error(1) inserting record into database: ".$e);
            $this->conn->rollback();
            return 0; // error
        }
    }

    private function insertFormData()
    {/*
      * insert an empty view record into the database, where a view can contain one or more linked tables.
      * result: the id of the view or 0 (error)
      */
        $id = 0;
        $customer = $this->session->get('customer');
        $this->conn->beginTransaction();

        try
        {/* initialize 1: get tables used in the query */
            $tabledef = $this->conn->fetchAll("explain select * from $customer.$this->formview");
            foreach ($tabledef as $values) {
                $this->mytabs[$values["table"]] = array();
            }

        /*  initialize 2: get field names and set default values */
            foreach ($this->mytabs as $key => $values)
            {/* for each table in the view */
                $sql = "show columns from $customer.$key";
                $tabledef = $this->conn->fetchAll( $sql );
                foreach ($tabledef as $idx => $arr)
                {/* set field => value pairs */
                    if ($this->constraint != "")
                    {/* use case 1: one to many relationship */
                        if ($arr["Field"]=$this->constraint_key) {
                            $this->mytabs[$key][$arr["Field"]] = $this->constraint_id; // parent key
                        } else {
                            $this->mytabs[$key][$arr["Field"]] = "NULL"; // instead of default
                        }
                    } else
                    {/* normal: insert record */
                        $this->mytabs[$key][$arr["Field"]] = "NULL"; // instead of default
                    }
                }
            }
        /*  insert one or more tables */
            switch (count($this->mytabs)) {
            case 0: /* nothing to insert */
                $this->session->set("errormsg","Error(1) cannot insert record in database.");
                $this->conn->rollback();
                return 0; // error

            case 1: /* insert one table */
                foreach ($this->mytabs as $table => $valuepairs)
                {
                    $id = $this->insertEmptyRecord($customer, $table, $valuepairs);
                }
                $this->conn->commit();
                return $id; // success

            default: /* insert more than one table */
                while (count($this->mytabs) > 0) {
                    $temp = count($this->mytabs);
                    $acntr = 0; // counts the number of actions in each loop
                    reset($this->mytabs); // rewind pointer to beginning
                    foreach ($this->mytabs as $table => $valuepairs)
                    {/* for each table in the form view */
                        $unresolved = 0;
                        foreach ($valuepairs as $key => $value)
                        {/* search each column for unresolved (NULL) foreign keys (convention = tablename_fk) */
                            if ((strpos($key, "_fk") !== false) and ($value == "NULL")) {
                                $unresolved +=1;
                            }
                        }
                        if ($unresolved == 0)
                        {/* child record: insert $valuepairs to database $customer, $table */
                            $acntr += 1; // increment counter
                            $id = $this->insertEmptyRecord($customer, $table, $valuepairs);
                            if ($id > 0) {
                                $temp = $this->setForeignKey($table."_fk", $id, $this->mytabs);
                                unset($this->mytabs[$table]);
                                /* success, drop through or loop again */
                            } else {
                                $this->session->set("errormsg","Error(2) inserting record into database (record id=0).");
                                $this->conn->rollback();
                                return 0; // error
                            }
                        }
                    }
                if ($acntr == 0) {
                    $this->session->set("errormsg","Error(4) Fehler in view, nicht alle Tabellen sind vorhanden!");
                    $this->conn->rollback();
                    return 0; // error
                }
                }
                $this->conn->commit();
                return $id; // success, the id is the views key (first parent id)
            }

        } catch (PDOException $e) {
            $this->session->set("errormsg","Error(3) inserting record into database: ".$e);
            $this->conn->rollback();
            return 0; // error
        }
    }

    private function getDefaultID()
    {/*
      * retrieve the default (first) id in the list view from the customer database
      * return: id 1..10E11 (success) 0 and error message (failure)
      */
        $customer = $this->session->get('customer');
        $sql = "select $this->pkey from $customer.$this->listview limit 0, 2";
        $mylist = array(); // initialize array

        try {
            // make a database call to get the meta data
            $connection = $this->container->get('database_connection');
            $mylist = $connection->fetchAll( $sql );
            if (count($mylist) == 0) {
                $this->session->set("errormsg","Leere Tabelle (2) ".$this->formview." keine Werte zum anzeigen (Hinzufügen?).");
                return 0;
            } else {
                return $mylist[0][$this->pkey];
            }

        } catch (PDOException $e) {
            $this->session->set("errormsg","Cannot access database : ".$e);
            return 0;
        }
    }

    private function getEscapeString($raw)
    {/*
      * escape the raw html request info, prevent sql injection.
      * NOT NEEDED, already in DBAL code:
      *   double quote rendered as &quot;
      *   single quote rendered as &#39;
      *   left bracket < rendered as &lt;
      *   right bracket > rendered as &gt;
      *   etc.
      * THESE ARE TEST CASES
      *
      * todo: check my.cnf for sql-mode=NO_BACKSLASH_ESCAPES
      * potential for multi-byte attacks
      */
        return $raw;
    }

    private function validDate($value)
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

    private function validTime($value)
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

    private function validDateTime($value)
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

    private function localizeDate($value)
    {/*
      * convert the MySQL compliant date strings into de-CH localized date strings
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

    private function delocalizeDate($value)
    {/*
      * convert the de-CH localized date strings into MySQL compliant date strings
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

    private function localizeTime($value)
    {/*
      * convert the MySQL compliant time strings into de-CH localized time strings
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

    private function delocalizeTime($value)
    {/*
      * convert the de-CH localized time strings into MySQL time strings
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

    private function localizeDateTime($value)
    {/*
      * convert the MySQL compliant date strings into de-CH localized date strings
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

    private function delocalizeDateTime($value)
    {/*
      * convert the de-CH localized datetime strings into MySQL compliant datetime strings
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

    private function validate($value, $idx)
    {/*
      * Standard Validation for AutoForms, not to be confused with callback validation
      * validate the request value for $this->myform[$idx]
      * $value is written to $this->myform[$idx]["Value"]
      * error message is written to $this->myform[$idx]["Error"]
      * return = true if no error encountered
      */
        $err = "";
        switch ($this->myform[$idx]['Rendered']) {
            case 'number':
                if (($this->myform[$idx]["Null"] == "NO") and ($value == "")) {
                    $err = "Validierungs Fehler: ein leeren Eintrag ist hier nicht erlaubt.";
                } elseif (($value != "") and (is_numeric($value) != true)) {
                        $err = "Validierungs Fehler: dies ist kein Integerzahl.";
                }
                break;

            case 'checkbox':
                if (($this->myform[$idx]["Null"] == "NO") and ($value == "")) {
                    $err = "Validierungs Fehler: ein leeren Eintrag ist hier nicht erlaubt.";
                } elseif (($value != "0") and ($value != "1")) {
                    $err = "Validierungs Fehler, ungültige Wert($value) in tinyint.";
                }
                break;

            case 'jq_datepicker':
            case 'jq_birthdaypicker':
            case 'jq_daterangepicker':
                if (($this->myform[$idx]["Null"] == "NO") and ($value == "")) {
                    $err = "Validierungs Fehler: ein leeren Eintrag ist hier nicht erlaubt.";
                } elseif (($value != "") and ($this->validDate($value) == false)) {
                    $err = "Validierungs Fehler, ungültiges Datum oder Format (tt.mm.jjjj).";
                } else {
                    // convert de-CH format (dd.mm.yyyy) to MySQL format (yyyy-mm-dd)
                    $value = $this->delocalizeDate($value);
                }
                break;

            case 'jq_datetimepicker':
                if (($this->myform[$idx]["Null"] == "NO") and ($value == "")) {
                    $err = "Validierungs Fehler: ein leeren Eintrag ist hier nicht erlaubt.";
                } elseif (($value != "") and ($this->validDateTime($value) == false)) {
                    $err = "Validierungs Fehler, ungültiges Datum/Zeit oder Format (tt.mm.jjjj hh:mm).";
                } else {
                    // convert de-CH format (dd.mm.yyyy) to MySQL format (yyyy-mm-dd)
                    $value = $this->delocalizeDateTime($value);
                }
                break;

            case 'jq_timepicker':
                if (($this->myform[$idx]["Null"] == "NO") and ($value == "")) {
                    $err = "Validierungs Fehler: ein leeren Eintrag ist hier nicht erlaubt.";
                } elseif (($value != "") and ($this->validTime($value) == false)) {
                    $err = "Validierungs Fehler, ungültiges Zeitformat (hh:mm).";
                }
                break;

            case 'text':
            case 'textarea':
                if (($this->myform[$idx]["Null"] == "NO") and ($value == "")) {
                    $err = "Validierungs Fehler: ein leeren Eintrag ist hier nicht erlaubt.";
                } else {
                    $len =  $this->myform[$idx]["Length"];
                    if (($len > 0) and (strlen($value) > $len)) {
                        $err = "Validierungs Fehler: Feldinhalt ist länger als erlaubt($len).";
                    }
                }
                break;

            default:
                $err = "Validierungs Fehler: ungeprüfte Datentype '".$this->myform[$idx]["Type"]."'.";
        }
        $this->myform[$idx]["Value"] = $value; // write value to array
        $this->myform[$idx]["Error"] = $err;   // write error message to array
        return ($err != "");                   // true if error encountered
    }

    private function hasErrorsMyform()
    {/* tests for errors in myform: 0 = none, 1 = error encountered */
        foreach ($this->myform as $values) {
            if ($values["Error"] != "") {
                return 1;
            }
        }
        return 0;
    }

    private function copyFormData2Myform($cursor)
    {/*
      * macro code for appending record data to the form object
      */
        $record = $this->getFormData($cursor); // database record
        $idx = 0;
        foreach ($record as $name => $value) {
            switch ($this->myform[$idx]["Rendered"])
            {
                case 'jq_daterangepicker':
                case 'jq_birthdaypicker':
                case 'jq_datepicker':
                $this->myform[$idx]["Value"] = $this->localizeDate($value);
                    break;
                case 'jq_datetimepicker':
                    $this->myform[$idx]["Value"] = $this->localizeDateTime($value);
                    break;
                default:
                    $this->myform[$idx]["Value"] = $value; // copy column value to form object
            }
            $idx += 1;
        }
    }

    private function copyReadonly2Myform()
    {/*
      * macro code for setting all fields in myform to readonly
      */
        foreach ($this->myform as $key => $values)
        {/* set all fields as readonly */
            $this->myform[$key]["Readonly"] = true;
        }
    }

    private function getIndividualID()
    {/*
      * retrieve the cursor of the forms view with the constraint applied
      * return the records cursor (pkey) or zero if none found
      */
        $customer = $this->session->get('customer');
        $sql = "select $this->pkey from $customer.$this->formview";
        if ($this->constraint != "") {
            $sql .= " where $this->constraint";
        }

        try {
            // make a database call to get the data
            $connection = $this->container->get('database_connection');
            $mylist = $connection->fetchAll( $sql );
            $cntr = count($mylist);
            if ($cntr == 0)
            {/* record not found */
                $this->session->set("errormsg",
                    "Leere Tabelle(1) ".$this->formview." keine Werte zum anzeigen (Hinzufügen?).");
                return 0;
            } elseif ($cntr == 1)
            {/* found exactly one record */
                return $mylist[0][$this->pkey];
            } else
            {/* found > 1 records */
                $this->session->set("errormsg",
                    "Mehrdeutige Abfrage ($sql), mehrere($cntr) Datensätze gefunden.");
                return $mylist[0][$this->pkey];
            }

        } catch (PDOException $e) {
            $this->session->set("errormsg","Cannot access database : ".$e);
            return 0;
        }
    }

    private function getViewSize()
    {/*
      * retrieve the size of the view from the customer database
      * return: id 1..10E11 (success) 0 and error message (failure)
      */
        $customer = $this->session->get('customer');
        $constraint = "";
        if ($this->constraint != "") {
            $constraint = "where ".$this->constraint." ";
        }
        $sql = "select count(*) from $customer.$this->formview $constraint";
        try
        {/* make a database call to get the meta data */
            $connection = $this->container->get('database_connection');
            $mylist = $connection->fetchAll( $sql );
            return $mylist[0]["count(*)"];
        }
        catch (PDOException $e)
        {
            $this->session->set("errormsg","Cannot access database : ".$e);
            return 0;
        }
    }

    private function getCursorOffset($route)
    {/*
      * Calculate the offset of the record [cursor] in the list view in the customer database
      * Note: using the @rownum methode with MySQL Innodb doesn't work properly (instable sort mechanism)
      * Return: id 1..10E11 (success) 0 and error message (failure)
      */
        $cursor = $this->session->get("cursor/$route");
        $customer = $this->session->get('customer');
        $constraint = "";
        if ($this->constraint != "") {
            $constraint = "where ".$this->constraint." ";
        }
        $sql = "select $this->pkey from $customer.$this->listview $constraint";
        try
        {/* make a database call to get the meta data */
            $connection = $this->container->get('database_connection');
            $mylist = $connection->fetchAll( $sql );
            foreach ($mylist as $key => $value) {
                if ($value[$this->pkey] == $cursor) {
                    return $key; // success offset for $cursor
                }
            }
            $this->session->set("errormsg","Cannot find offset for cursor ".$cursor);
            return 0;
        }
        catch (PDOException $e)
        {
            $this->session->set("errormsg","Cannot access database : ".$e);
            return 0;
        }
    }

    public function makeCollectionObjectStates($route)
    {/*
      * state machine for one list object (collection = true)
      * inputs: session > mode and session > action and
      *         session > cursor/$route (selected in list mode)
      * outputs: session > mode and session > action
      * database: new records, deleted records (modify is external)
      */

    // initialize variables
        $tixi = $this->container->getParameter('tixi');
        $this->setMetaData($route);

    // state machine for a list object
        $action = ($this->session->get('action'));
        if ($action=='') { $this->session->set('mode', $tixi['mode_select_list']); };
        if ($this->session->get('mode') == $tixi['mode_select_list']) {
            if ($action == '')
            {/* action code for the first call */
                /* this is managed in the ListBuilder service */
            }
            elseif ($action == 'add')
            {/* action code for a new list object ------------------------------------- */
                $cursor = $this->insertFormData();
                if ($cursor > 0)
                {/* set cursor to match the inserted object(s) */
                    $this->session->set("cursor/$route", $cursor);
                    $this->session->set('tainted', "$route:$cursor");
                    $this->copyFormData2Myform($cursor);
                    /* set new state */
                    $this->session->set('mode', $tixi['mode_edit_in_list']); // set new state
                }
            }
            elseif ($action == 'modify')
            {/* action code for modify list object ------------------------------------- */
                $cursor = $this->session->get("cursor/$route");
                $this->copyFormData2Myform($cursor);
                /* set new state */
                $this->session->set('mode', $tixi['mode_edit_in_list']); // set new state
            }
            elseif ($action == 'delete')
            {/* action code for delete list object ---------------------------------- */
                $cursor = $this->session->get("cursor/$route");
                $this->deleteFormData($cursor); // delete the database record
                $cursor = $this->getDefaultID();
                $this->session->set("cursor/$route", $cursor); // set new cursor (default)
            }
            elseif ($action == 'select')
            {/* action code for select (changed cursor) ----------------------------- */
                /* this is managed in the ListBuilder service */
            }
            elseif ($action == 'begin')
            {/* action code for begin (first page) ---------------------------------- */
                $this->session->set("offset/$route", 0);
            }
            elseif ($action == 'previous')
            {/* action code for previous page --------------------------------------- */
                $offset = $this->session->get("offset/$route") - $tixi["rowcount"];
                $offset = ($offset > 0) ? $offset : 0;
                $this->session->set("offset/$route", $offset );
            }
            elseif ($action == 'selected')
            {/* action code for selected page --------------------------------------- */
                $offset = $this->getCursorOffset($route);
                if ($offset > 0) {
                    $this->session->set("offset/$route", $offset);
                }
            }
            elseif ($action == 'next')
            {/* action code for next page ------------------------------------------- */
                $offset = $this->session->get("offset/$route") + $tixi["rowcount"];
                $size = $this->getViewSize();
                if ($offset < $size) {
                    $this->session->set("offset/$route", $offset);
                }
            }
            elseif ($action == 'end')
            {/* action code for end (last page) ------------------------------------- */
                $offset = $this->getViewSize() - $tixi["rowcount"];
                $offset = ($offset > 0) ? $offset : 0;
                $this->session->set("offset/$route", $offset);
            }
            elseif ($action == 'filter')
            {/* action code for filter (changed filter criteria) -------------------- */
                $dummy = 'stop'; // don't do anything special: overrides << < [] > >>
            }
            elseif ($action == 'print')
            {/* action code for printing -------------------------------------------- */
                $this->session->set('errormsg', 'Die Druckfunktion ist noch nicht implementiert.');
            }
            else
            {// unexpected action in this state
                $this->session->set('errormsg',
                    'Fehler(1): illegaler action '.$this->session->get('action').' in state '.$this->session->get('mode'));
            }
        }
        elseif ($this->session->get('mode') == $tixi['mode_edit_in_list'])
        {/* action code for edit state */
            if ($action == 'save')
            {/* action code for saving the form data to the database -------------------- */
                $cursor = $this->session->get("cursor/$route");
                $record = $this->getFormData($cursor); // get database record
                $idx = 0;
                foreach ($record as $name => $value) {
                    $this->myform[$idx]["Value"] = $value;  // set database value
                    $this->myform[$idx]["Error"] = "";      // reset error
                    $this->myform[$idx]["Change"] = false;  // mark as not changed
                    if (array_key_exists($name, $_REQUEST))
                    {/* field in request */
                        $secure = $_REQUEST[$name]; // quotes and html bracket escaped in DBAL
                        if ($value != $secure)
                        {/* validate data in the request */
                            $err = $this->validate($secure, $idx);
                            $this->myform[$idx]["Change"] = true;
                        } else
                        {/* validate data in the database */
                            $err = $this->validate($value, $idx);
                            $this->myform[$idx]["Change"] = false;
                        }
                    }
                    $idx += 1;
                }
                if (is_callable($this->callback)) {
                    $this->myform = call_user_func($this->callback, $this->myform);
                }
                if ($this->hasErrorsMyform() > 0) {
                    $this->session->set('errormsg', 'Validierungsfehler in ein oder mehrer Felder, bitte korrigieren.');
                } else
                {/* update database */
                    $this->setFormData($cursor);
                    $this->session->set('tainted', $tixi['undefined']); // reset tainted
                    $this->session->set('mode', $tixi['mode_select_list']); // set new state
                }
            }
            elseif ($action == 'cancel')
            {/* action code for canceling ------------------------------------------ */
                $cursor = $this->session->get("cursor/$route");
                $tainted = $this->session->get('tainted');
                if ($tainted != $tixi['undefined'])
                {/* the inserted object has not been properly validated */
                    $a = explode(":", $tainted);
                    if (count($a)==2) {
                        if ($a[0] == $route) {
                            $this->deleteFormData($cursor); // delete the database record
                            $cursor = $this->getDefaultID(); // get new cursor (default)
                            $this->session->set("cursor/$route", $cursor);
                            $this->session->set('errormsg', "Objekt Nr. $a[1] gelöscht, nicht validiert.");
                        } else {
                            $this->session->set('errormsg', "Fehler in Seite $route, Objekt Nr.. $a[1] nicht validiert.");
                        }
                    }
                }
                $this->session->set('tainted', $tixi['undefined']); // reset tainted
                $this->session->set('mode', $tixi['mode_select_list']); // set new state
            } else
            {// unexpected action in this state
                $this->session->set('errormsg',
                    'Fehler(2): illegaler action '.$this->session->get('action').' in state '.$this->session->get('mode'));
            }
        }
        else
        {// unexpected state
            $this->session->set('errormsg',
                'Fehler(3): illegaler Zustand '.$this->session->get('mode'));
        }
    }

    public function makeIndividualObjectStates($route)
    {/*
      * state machine for one single object (collection = false)
      * inputs: session > mode and session > action and
      *         constraint (defines one or zero records)
      * outputs: session > mode and session > action
      * database: new records, or modified records
      */

        /* initialize variables */
        $tixi = $this->container->getParameter('tixi');
        $this->setMetaData($route);

        /* state machine for an individual object */
        $action = ($this->session->get('action'));
        if ($action=='') { $this->session->set('mode', $tixi['mode_read_record']); }
        if ($this->session->get('mode') == $tixi['mode_read_record'])
        {/* individual object read mode  */
            if ($action == '')
            {/* action code for the first call (read only) -------------------------- */
                $cursor = $this->getIndividualID();
                if ($cursor == 0)
                {/* cursor not found, set error message */
                    $this->session->set('errormsg',
                        'Kein Datensatz gefunden. Leere Tabelle?');
                } else
                {/* cursor found, get record from database */
                    $this->session->set("cursor/$route", $cursor);
                    $this->session->set("context/$route", MenuTree::getCell($route, "CAPTION")."[$cursor]");
                    $this->copyFormData2Myform($cursor);
                }
                $this->copyReadonly2Myform();
            }
            elseif ($action == 'modify')
            {/* action code for modify --------------------------------------------- */
                $cursor = $this->session->get("cursor/$route");
                $this->copyFormData2Myform($cursor);
                /* set new state */
                $this->session->set('mode', $tixi['mode_edit_record']); // set new state
            }
            elseif ($action == 'print')
            {/* action code for print ---------------------------------------------- */
                $cursor = $this->session->get("cursor/$route");
                $this->copyFormData2Myform($cursor);
                $this->copyReadonly2Myform();
                $this->session->set('errormsg',
                    'Die Druckfunktion ist noch nicht implementiert.');
            }
            else
            {/* unexpected action in this state */
                $this->session->set('errormsg',
                    'Fehler(6): illegaler Aktion '.$this->session->get('action')." in Zustand ".$this->session->get('mode'));
            }
        }
        elseif ($this->session->get('mode') == $tixi['mode_edit_record'])
        {/* individual object edit mode (modify) */
            if ($action == 'save')
            {/* action code for saving the changed data ----------------------------- */
                $cursor = $this->session->get("cursor/$route");
                $record = $this->getFormData($cursor); // get database record
                $idx = 0;
                foreach ($record as $name => $value) {
                    $this->myform[$idx]["Value"] = $value;  // set database value
                    $this->myform[$idx]["Error"] = "";      // reset error
                    $this->myform[$idx]["Change"] = false;  // mark as not changed
                    if (array_key_exists($name, $_REQUEST))
                    {/* field in request */
                        $secure = $_REQUEST[$name]; // quotes and html bracket escaped in DBAL
                        if ($value != $secure)
                        {/* validate data in the request */
                            $err = $this->validate($secure, $idx);
                            $this->myform[$idx]["Change"] = true;
                        } else
                        {/* validate data in the database */
                            $err = $this->validate($value, $idx);
                            $this->myform[$idx]["Change"] = false;
                        }
                    }
                    $idx += 1;
                }
                if (is_callable($this->callback)) {
                    $this->myform = call_user_func($this->callback, $this->myform);
                }
                if ($this->hasErrorsMyform() > 0) {
                    $this->session->set('errormsg',
                        'Validierungsfehler in ein oder mehrer Felder, bitte korrigieren.');
                } else
                {/* update database */
                    $this->setFormData($cursor);
                    $this->copyReadonly2Myform();
                    /* set new mode */
                    $this->session->set('mode', $tixi['mode_read_record']); // set new state
                }
            }
            elseif ($action == 'cancel')
            {/* action code for canceling the changes made ------------------------- */
                /* action code for canceling */
                $cursor = $this->session->get("cursor/$route");
                $this->copyFormData2Myform($cursor);
                $this->copyReadonly2Myform();
                /* set new mode */
                $this->session->set('mode', $tixi['mode_read_record']); // set new state
            }
            elseif ($action == 'print')
            {/* action code for print --------------------------------------------- */
                $cursor = $this->session->get("cursor/$route");
                $this->copyFormData2Myform($cursor);
                $this->session->set('errormsg',
                    'Die Druckfunktion ist noch nicht implementiert.');
            }
            else
            {/* unexpected action in this state */
                $this->session->set('errormsg',
                    'Fehler(5): illegaler Aktion '.$this->session->get('action')." in Zustand ".$this->session->get('mode'));
            }
        } else
        {/* unexpected state encountered */
            $this->session->set('errormsg',
                'Fehler(4): illegaler Zustand '.$this->session->get('mode'));
        }
    }
}