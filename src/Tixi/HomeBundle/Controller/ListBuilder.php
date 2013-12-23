<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martin jonasse
 * Date: 01.10.13
 * Time: 07:29
 * initial file
 */
// src\Tixi\HomeBundle\Controller\ListBuilder.php
// 04.10.2013 martin jonasse upgrade code w. uppercase, cursors, filters, route
// 04.11.2013 martin jonasse upgrade cursor to structured namespace
// 96.11.2013 martin jonasse added $this->session
// 19.11.2013 martin jonasse rolled back filter methode, MATCH ... AGAINST is instable
// 22.12.2013 martin jonasse added pagination in getTable

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ListBuilder extends Controller
{ /*
   * class for displaying a SQL view as a HTML table on the screen,
   * the user can select rows and / or filter these rows
   * input: the view, all other inputs are stored in the session
   * feature: the number of rows displayed is limited to 30
   */
    protected $container;      // container
    protected $listview;       // input (name of the MySQL view)
    protected $pkey;           // input (name of the primary key, defines record)
    protected $constraint="";  // input (optional where clause)
    protected $session;        // input&output
    protected $list;           // output

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setListView($value)
    {/*
      * MANDATORY: view name, must match an existing MySQL view in the customer database
      */
        $this->listview = $value;
        // init other variables
        $this->list = array();
        $this->session = new session;
    }

    public function setPkey($value)
    {/*
      * MANDATORY: primary key name for the list view
      */
        $this->pkey = $value;
    }

    public function setConstraint($value="")
    {/*
      * OPTIONAL: constraint (SQL expression) for the view
      */
        $this->constraint = $value;
    }

    private function getTable($route)
    {/*
      * retrieve headers for the view from the customer database
      * return: array of headers (success) empty array and error message (failure)
      */
        $customer = $this->session->get('customer');
        $sql = "select * from $customer.$this->listview ";
        $constraint = $this->constraint;
        $filter = $this->session->get("filter/$route");
        if (($constraint != "") or ($filter != ""))
        {
            $sql .= "where "; // append where
        }
        if ($constraint != "")
        {
            $sql .= $this->constraint." "; // append constraint
            if ($filter != "") { $sql .= "and "; }
        }
        $mylist = array(); // initialize array

        try
        {/* initialize variables */
            $connection = $this->container->get('database_connection');

            if ($filter != "")
            {/*
              * get all varchar and text fieldnames that need to be filtered
              */
                $match ="";
                $textfields = $connection->fetchAll(
                    "show fields from $customer.$this->listview where type like 'varchar%' or type like 'text%'");
                if (count($textfields) >0) { // indeed there are fields to be filtered
                    foreach($textfields as $key => $value) {
                        if ($key == 0) {
                            $match .= "(".$value["Field"]." like '%".$filter."%' ";
                        } else {
                            $match .= "or ".$value["Field"]." like '%".$filter."%' ";
                        }
                    }
                    $match .= ")";
                }
                $sql .= $match;

                $mylist = $connection->fetchAll( $sql );

                if ((count($mylist) == 0) and ($this->session->get("errormsg") == "")) {
                    $this->session->set("errormsg",
                        "Leere Tabelle, keine Werte zum anzeigen (Filter?). [1]");
                }
                return $mylist; // header information
            }
            else
            {/* limit the unfiltered table length to 20 records */
                $rowcount = $this->container->getParameter('tixi')['rowcount'];
                $sql .= "LIMIT ".$this->session->get("offset/$route").", ".($rowcount+1);

                $mylist = $connection->fetchAll( $sql );
                $count = count($mylist);

                if (($count == $rowcount+1) or ($this->session->get("offset/$route")>0))
                {/* criteria for displaying the buttons << < [] > >> */
                    $this->session->set("hasNext/$route", true);
                } else {
                    $this->session->set("hasNext/$route", false);
                }

                if ($count == 0)
                {
                    $this->session->set("errormsg", "Leere Tabelle, keine Werte zum anzeigen (Filter?). [2]");
                }
                elseif ($count = $rowcount+1)
                {
                    return array_slice($mylist, 0, $rowcount);
                }
                return $mylist;
           }

        } catch (PDOException $e) {
            $this->session->set("errormsg","Cannot access database : ".$e);
            return array(); // empty headers
        }
    }

    private function getDefaultID()
    {/*
      * retrieve the default (first) id in the view from the customer database
      * return: id 1..10E11 (success) 0 and error message (failure)
      */
        $customer = $this->session->get('customer');
        $constraint = "";
        if ($this->constraint != "") {
            $constraint = "where ".$this->constraint." ";
        }
        $sql = "select $this->pkey from $customer.$this->listview $constraint limit 0, 2";
        $mylist = array(); // initialize array

        try {
            // make a database call to get the meta data
            $connection = $this->container->get('database_connection');
            $mylist = $connection->fetchAll( $sql );
            if (count($mylist) == 0) {
                $this->session->set("errormsg",
                    "Leere Tabelle, keine Werte zum anzeigen (Hinzufügen?). [1]");
                return 0;
            } else {
                return $mylist[0][$this->pkey];
            }

        } catch (PDOException $e) {
            $this->session->set("errormsg","Cannot access database : ".$e);
            return 0;
        }
    }

    private function checkChildID ($route)
    {/*
      * check that the cursor selected for this route
      * belongs to the current parent object: return true
      * otherwise reset the cursor to the first id mylist
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
            if (count($mylist) == 0)
            {/* empty list, no match for $cursor possible */
                $this->session->set("errormsg",
                    "Leere Tabelle, keine Werte zum anzeigen (Hinzufügen?). [2]");
                return false;
            } else
            {/* test values in the array */
                foreach ($mylist as $key => $values){
                    if ($values[$this->pkey] == $cursor){
                        return true; // cursor OK
                    }
                }
                $this->session->set("cursor/$route", $mylist[0][$this->pkey]); // reset to first id in mylist
                return true; // cursor reseted
            }
        }
        catch (PDOException $e)
        {
            $this->session->set("errormsg","Cannot access database : ".$e);
            return false; // failed
        }
    }

    private function setSubject($route)
    {/*
      * set the session variable "subject" and "context",
      * based on data in $this->list
      */
        $cursor = $this->session->get("cursor/$route");
        $subject = MenuTree::getCell($route, "CAPTION").": ";
        /* add names to subject */
        foreach ($this->list as $key => $values) {
            if ($cursor == $values[$this->pkey])
            {/* found the selected row */
                foreach ($values as $col => $cell){
                    if ((strpos($col, "name")!==false) or (strpos($col, "betreff")!==false)){
                        $subject .= $cell." ";
                    }
                }
                break;
            }
        }
        /* if applicable, prefix subject with context info */
        $parent = menutree::getCell($route, "PARENT");
        if ($parent != "") {
            $subject = $this->session->get("context/$parent")." - ".$subject;
        }
        /* restrict length */
        if (strlen($subject) > 80) {
            $subject = substr($subject, 0, 77)."...";
        }
        /* return (persist) data */
        $this->session->set("subject", $subject);
        $this->session->set("context/$route", $subject);
    }

    public function makeList($route)
    {/*
      * make a list (array) with data (headers, values) from the customer database
      * call this before rendering the list
      */
        if (menutree::getCell($route, "PARENT") == "")
        {/* parent object */
            if (!$this->session->has("cursor/$route"))
            {/* get default */
                $this->session->set("cursor/$route", $this->getDefaultID());
            }
        }
        else
        {/* child object */
            $this->checkChildID($route);
        }

        $this->list = $this->getTable($route); /* get list data (table) */
        $this->setSubject($route);       /* set subject in the session */
    }

    public function getRows()
    {/*
      * return an array containing results from the database query,
      * but can also return an empty array
      */
        return $this->list;
    }

    public function getHeader()
    {/*
      * return an array containing the (uppercase) header data of the database query,
      * but can also return an empty array
      */
        if (count($this->list) == 0) {
            return array();
        } else {
            $myarray = array_keys($this->list[0]);
            foreach ($myarray as $key=>$value) {
                $myarray[$key] = strtoupper($value);
            }
            return $myarray;
        }
    }
}