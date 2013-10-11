<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martin jonasse
 * Date: 01.10.13
 * Time: 07:29
 * initial file
 */
// src\Tixi\HomeBundle\Controller\ListBuilder.php
// martin jonasse 04.10.2013 upgrade code w. uppercase, cursors, filters, route

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
    protected $container;   // container
    protected $view;        // input (name of the MySQL view)
    protected $pkey;        // input (name of the primary key, defines record)
    protected $list;        // output

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setView($value)
    {/*
      * view name, must match an existing MySQL view in the customer database
      */
        $this->view = $value;
        $this->list = array();
    }

    public function setPkey($value)
    {/*
      * primary key name for the view
      */
        $this->pkey = $value;
        $this->list = array();
    }

    private function getTable($session)
    {/*
      * retrieve headers for the view from the customer database
      * return: array of headers (success) empty array and error message (failure)
      */
        $customer = $session->get('customer');
        $sql = "select * from $customer.$this->view ";
        if ( $session->get('filter') != '' )
        {
            $filter = $session->get('filter'); // continues below
        }
        $mylist = array(); // initialize array

        try {
            // make a database call to get the meta data
            $connection = $this->container->get('database_connection');
            if (isset($filter)) { // get fields that shall be filtered
                $sqlconditions = '';
                $textfields = $connection->fetchAll("show fields from $customer.$this->view where type like 'varchar%'");
                if (count($textfields) >0) { // indeed there are fields to be filtered
                    foreach($textfields as $key => $value) {
                        if ($key == 0) {
                            $sqlconditions .= "where ".$value["Field"]." like '%".$filter."%' ";
                        } else {
                            $sqlconditions .= "or ".$value["Field"]." like '%".$filter."%' ";
                        }
                    }
                }
                $sql .= $sqlconditions;
            }
            $mylist = $connection->fetchAll( $sql );
            if (count($mylist) == 0) {
                $session->set("errormsg","Leere Tabelle ".$this->view." keine Werte zum anzeigen (Filter?).");
            }
            return $mylist; // header information

        } catch (PDOException $e) {
            $session->set("errormsg","Cannot access database : ".$e);
            return array(); // empty headers
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
                return "0";
            } else {
                return $mylist[0][$this->pkey];
            }

        } catch (PDOException $e) {
            $session->set("errormsg","Cannot access database : ".$e);
            return "0";
        }
    }

    public function makeList()
    {/*
      * make a list (array) with data (headers, values) from the customer database
      */
        $session = new session;

        $cursors = $session->get('cursors');
        $route = $session->get('route');
        if (!isset($cursors[$route])) { // previous id not set, get default
            $cursors[$route] = $this->getDefaultID($session);
            $session->set('cursors', $cursors);
        }

        $this->list = $this->getTable($session);
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