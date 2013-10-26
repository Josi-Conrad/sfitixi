<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 30.09.13
 * Time: 12:15
 */
// martin jonasse 23.10.2013 finished basic form functions add, modify, save, delete, and quit

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class FormStateBuilder extends Controller
{
    protected $container; // container
    protected $view;      // input (name of the MySQL view)
    protected $pkey;      // input (name of the primary key)
    protected $myform;    // meta plus values for rendering a form
    protected $mytabs;    // tables and value pairs for the insert function
    protected $conn;      // database connection
    protected $callback;  // callback fuction for validating the formdata

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setFormView($value)
    {/*
      * define the name of the view
      * the mysql database defines the data structure and constraints
      * please observe MySQL chapter 18.4.3 Updateable amd Insertable Views
      */
        $this->view = $value;
        // set defaults
        $this->conn = $this->get('database_connection');
        $this->pkey = null;
        $this->callback = null;
    }

    public function setPkey($value)
    {/*
      * define the name of the primary key for the $view
      */
        $this->pkey = $value;
    }

    public function setCallback($value=null)
    {/*
      * define the name of the callback function, default = empty
      */
        $this->callback = $value;
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

    private function getMetaData($session)
    {/*
      * retrieve the meta data for the table(s) from the customer database
      */
        $customer = $session->get('customer');

        try {
            // make a database call to get the meta data
            $rownr = 0;
            $sql = "show full columns from $customer.$this->view";
            $this->myform = $this->conn->fetchAll( $sql );

            // update meta data
            foreach ($this->myform as $key => $value) {

                // readonly?
                $postfix = substr($value["Field"], -3 ); // last three characters
                $readonly = (($postfix == '_id') or ($value == 'MID')); // primary key and migration id
                $this->myform[$key]["Readonly"] = $readonly;

                // hidden?
                $hidden = ($postfix == '_fk');
                $this->myform[$key]["Hidden"] = $hidden;

                // maxlength?
                $this->myform[$key]["Length"] = $this->getLen($value["Type"]);

                // basetype?
                $paranthesis = strpos($value["Type"], "(");
                $basetype = ($paranthesis !== false) ? substr($value["Type"], 0, $paranthesis ) : $value["Type"];
                $this->myform[$key]["Basetype"] = $basetype;

                // map to (render as) html input type
                if       ($basetype == 'int')        { $this->myform[$key]["Rendered"] = 'text';
                } elseif ($basetype == 'varchar')    { $this->myform[$key]["Rendered"] = 'text';
                } elseif ($basetype == 'tinyint')    { $this->myform[$key]["Rendered"] = 'checkbox';
                } elseif ($basetype == 'mediumtext') { $this->myform[$key]["Rendered"] = 'textarea';
                } elseif ($basetype == 'text')       { $this->myform[$key]["Rendered"] = 'textarea';
                } else {                               $this->myform[$key]["Rendered"] = 'undefined';
                }

                // error message, previous value
                $this->myform[$key]["Error"] = ""; // empty
                $this->myform[$key]["Value"] = ""; // empty
            }

            return $this->myform;

        } catch (PDOException $e) {
            $session->set("errormsg","Cannot access database : ".$e);
            return "0";
        }
    }

    public function getFormMetaData()
    {/*
      * get the forms meta data, based upon the data in the (joined) table(s)
      * the submitted user data will be persisted to the database
      */
        return $this->myform;
    }

    private function getFormData($cursor)
    {/*
      * return an array containing the persistent data values for the form
      */
        $session = new session;
        $customer = $session->get('customer');

        try {
            // make a database call to get the meta data
            $sql = "select * from $customer.$this->view where $this->pkey=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue( 1, "$cursor", Type::INTEGER);
            $stmt->execute();
            // return an array with persistent data (query has only one record)
            return $stmt->fetch();

        } catch (PDOException $e) {
            $session->set("errormsg","Error reading the database: ".$e);
            return array();
        }

    }

    private function setFormData($cursor)
    {/*
      * update the data, changed by the user, in the customer database
      */
        $session = new session;
        $customer = $session->get('customer');

        try {
            $count = 0;
            foreach ($this->myform as $value) {
                if ($value["Change"]) {
                    // make a database call to update one changed data column
                    $sql = "UPDATE ".$customer.".".$this->view." SET ".$value["Field"]." = ? WHERE ".$this->pkey." = ?";
                    $count += $this->conn->executeUpdate($sql, array($value["Value"], $cursor));
                }
            }
            // return an array with persistent data (query has only one record)
            return $count;

        } catch (PDOException $e) {
            $session->set("errormsg","Error updating the database: ".$e);
            return 0;
        }
    }

    private function deleteFormData($cursor)
    {/*
      * delete a record from the database, not per view, but for each table in the view ...
      */
        $session = new session;
        $customer = $session->get('customer');

        /* get database record for the view, containg the table indices */
        $record = $this->getFormData($cursor);
        $idx = 0;
        foreach ($record as $name => $value) {
            $this->myform[$idx]["Value"] = $value; // copy column value to form object
            $idx += 1;
        }

        /* now try to delete all the table records in the view */
        try {
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
            return true; // success

        } catch (PDOException $e) {
            $session->set("errormsg","Error deleting record from database: ".$e);
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
      * set the foreign key ($fkey) to $fval anywhere in $myarray (whole)
      * return: number of keys set or 0 (none)
      */
        $cntr = 0; // initial counter value
        foreach ($myarray as $key => $value)
        {/* loop over local copy */
            if (array_key_exists($fkey, $value)) {
                if ($value[$fkey] = "NULL")
                {/* set record number */
                    $this->mytabs[$key][$fkey] = $fkid;
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
        try {
            $sql = $this->getInsertStatement($mycustomer, $mytable, $myarray);
            $this->conn->exec($sql); // insert empty record
            $id = $this->conn->lastInsertId(); // get record id
            return $id;
        }
        catch (PDOException $e) {
            $session->set("errormsg","Error(1) inserting record into database: ".$e);
            return 0; // error
        }
    }

    private function insertFormData()
    {/*
      * insert an empty view record into the database, where the view contains one or more linked tables.
      * result: the id of the view or 0 (error)
      */
        $session = new session;
        $customer = $session->get('customer');

        try
        {/* initialize 1: get tables used in the query */
            foreach ($this->myform as $key => $values) {
                if (strpos($values["Field"], "_id") !== false)
                {/* this is the primary key (convention='tablename_id') */
                    $temp = explode("_", $values["Field"])[0];
                    $this->mytabs[$temp] = array();
                }
            }

        /*  initialize 2: get field names and set default values */
            foreach ($this->mytabs as $key => $values)
            {/* for each table in the view */
                $sql = "show columns from $customer.$key";
                $tabledef = $this->conn->fetchAll( $sql );
                foreach ($tabledef as $idx => $arr)
                {/* set field => value pairs */
                    $this->mytabs[$key][$arr["Field"]] = "NULL"; // instead of default
                }
            }

        /*  loop: insert foreign keys and insert parent / child records */
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
                        $id = $this->insertEmptyRecord($customer, $table, $valuepairs);
                        if ($id > 0) {
                            $temp = $this->setForeignKey($table."_fk", $id, $this->mytabs);
                            unset($this->mytabs[$table]);
                            /* success, drop through or loop again */
                        } else {
                            $session->set("errormsg","Error(2) inserting record into database (record id=0).");
                            return false;
                        }
                    }
                }
            }
            return $id; // success, the id is the views key (first parent id)

        } catch (PDOException $e) {
            $session->set("errormsg","Error(3) inserting record into database: ".$e);
            return 0; // error
        }
    }

    private function getDefaultID($session)
    {/*
      * retrieve the default (first) id in the view from the customer database
      * return: id 1..10E11 (success) 0 and error message (failure)
      */
        $customer = $session->get('customer');
        $sql = "select $this->pkey from $customer.$this->view limit 0, 2";
        $mylist = array(); // initialize array

        try {
            // make a database call to get the meta data
            $connection = $this->container->get('database_connection');
            $mylist = $connection->fetchAll( $sql );
            if (count($mylist) == 0) {
                $session->set("errormsg","Leere Tabelle ".$this->view." keine Werte zum anzeigen (Hinzufügen?).");
                return 0;
            } else {
                return $mylist[0][$this->pkey];
            }

        } catch (PDOException $e) {
            $session->set("errormsg","Cannot access database : ".$e);
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

    private function validate($value, $idx)
    {/*
      * validate the request value for $this->myform[$idx]
      * $value is written to $this->myform[$idx]["Value"]
      * error message is written to $this->myform[$idx]["Error"]
      * return = true if no error encountered
      */
        $err = "";
        switch ($this->myform[$idx]['Basetype']) {
            case 'int':
                if (($this->myform[$idx]["Null"] == "NO") and ($value == "")) {
                    $err = "Validierungs Fehler: ein leeren Eintrag ist hier nicht erlaubt.";
                } elseif (is_numeric($value) != true){
                    $err = "Validierungs Fehler: dies ist kein Integerzahl.";
                }
                break;

            case 'tinyint':
                if (($this->myform[$idx]["Null"] == "NO") and ($value == "")) {
                    $err = "Validierungs Fehler: ein leeren Eintrag ist hier nicht erlaubt.";
                } elseif (($value != "0") and ($value != "1")) {
                    $err = "Validation error, illegal value=$value in tinyint.";
                }
                break;

            case 'text':
            case 'varchar':
            case 'mediumtext':
                if (($this->myform[$idx]["Null"] == "NO") and ($value == "")) {
                    $err = "Validierungs Fehler: ein leeren Eintrag ist hier nicht erlaubt.";
                } elseif ($this->myform[$idx]["Length"] > 0) {
                    if (strlen($value) > $this->myform[$idx]["Length"]) {
                        $err = "Validierungs Fehler: Feldinhalt ist länger als erlaubt($this->myform[$idx]['Length']).";
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

    public function setListObjectStates($page)
    { /*
       * state machine for one list object (= one object of many!)
       * inputs: session > mode and session > action
       * outputs: session > mode and session > action
       * database: new records, deleted records (modify is external)
       */

    // initialize variables
        $session = new session;
        $tixi = $this->container->getParameter('tixi');
        $this->myform = $this->getMetaData($session);

    // state machine for a list object
        $action = ($session->get('action'));
        if ($action=='') { $session->set('mode', $tixi['mode_select_list']); };
        if ($session->get('mode') == $tixi['mode_select_list']) {
            if ($action == '') {
                // action code for the first call

            } elseif ($action == 'add') {
                // action code for a new list object
                $idx = $this->insertFormData();
                if ($idx > 0)
                {/* set cursor to match the inserted object(s) */
                    $cursors = $session->get('cursors');
                    $cursors[$page] = $idx;
                    $session->set('cursors', $cursors);
                    $session->set('tainted', "$page:$idx");
                /*  get persistent data and set mode */
                    $record = $this->getFormData($cursors[$page]); // database record
                    $idx = 0;
                    foreach ($record as $name => $value) {
                        $this->myform[$idx]["Value"] = $value; // copy column value to form object
                        $idx += 1;
                    }
                    $session->set('mode', $tixi['mode_edit_in_list']); // set new state
                }

            } elseif ($action == 'modify') {
                // action code for modify list object
                $cursors = $session->get('cursors');
                $record = $this->getFormData($cursors[$page]); // database record
                $idx = 0;
                foreach ($record as $name => $value) {
                    $this->myform[$idx]["Value"] = $value; // copy column value to form object
                    $idx += 1;
                }
                $session->set('mode', $tixi['mode_edit_in_list']); // set new state

            } else { // state mode_select_list remains the same
                if ($action == 'delete') {
                    // action code for delete list object
                    $cursors = $session->get('cursors');
                    $this->deleteFormData($cursors[$page]); // delete the database record
                    $cursors[$page] = $this->getDefaultID($session); // get new default cursor
                    $session->set('cursors', $cursors); // write it back to the session

                } elseif ($action == 'select') {
                    // action code for select (changed cursor)

                } elseif ($action == 'filter') {
                    // action code for filter (changed filter criteria)

                } elseif ($action == 'print') {
                    // action code for printing
                    $session->set('errormsg',
                        'Die Druckfunktion ist noch nicht implementiert.');

                } else {
                    $session->set('errormsg',
                        'Fehler(1): illegaler action '.$session->get('action').' in state '.$session->get('mode'));
                }
                // action code for displaying list

            }
        } elseif ($session->get('mode') == $tixi['mode_edit_in_list']) {
            if ($action == 'save')
            {/* action code for saving the form data to the database */
                $cursors = $session->get('cursors');
                $record = $this->getFormData($cursors[$page]); // database record
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
                $ecnt = 0;
                foreach ($this->myform as $values) {
                    if ($values["Error"] != "") {
                        $ecnt = 1;
                        break;
                    }
                }
                if ($ecnt >= 1) {
                    $session->set('errormsg', 'Validierungsfehler in ein oder mehrer Felder, bitte korrigieren.');
                } else {
                    $this->setFormData($cursors[$page]); // update database
                    $session->set('mode', $tixi['mode_select_list']); // set new state
                }

            } elseif ($action == 'cancel') {
                // action code for canceling
                $cursors = $session->get('cursors');
                $tainted = $session->get('tainted');
                if ($tainted != $session->get('undefined'))
                {/* the inserted object has not been properly validated */
                    $a = explode(":", $tainted);
                    if ($a[0] == $page) {
                        $this->deleteFormData($cursors[$page]); // delete the database record(s)
                        $cursors[$page] = $this->getDefaultID($session); // get new default cursor
                        $session->set('cursors', $cursors); // write it back to the session
                        $session->set('errormsg', "Deleted object no. $a[1], not validated.");
                    } else {
                        $session->set('errormsg', "Error on page $page, object no. $a[1] not validated.");
                    }
                }
                $session->set('tainted', $session->get('undefined'));
                $session->set('mode', $tixi['mode_select_list']); // set new state

            } else {
                $session->set('errormsg',
                    'Fehler(2): illegaler action '.$session->get('action').' in state '.$session->get('mode'));
            }
        } else {
            $session->set('errormsg',
                'Fehler(3): illegaler Zustand '.$session->get('mode'));
        }
    }
}