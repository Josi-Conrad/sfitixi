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
    protected $session;     // input&output
    protected $list;        // output

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setListView($value)
    {/*
      * view name, must match an existing MySQL view in the customer database
      */
        $this->view = $value;
        // init other variables
        $this->list = array();
        $this->session = new session;
    }

    public function setPkey($value)
    {/*
      * primary key name for the view
      */
        $this->pkey = $value;
        $this->list = array();
    }

    private function getTable()
    {/*
      * retrieve headers for the view from the customer database
      * return: array of headers (success) empty array and error message (failure)
      */
        $customer = $this->session->get('customer');
        $sql = "select * from $customer.$this->view ";
        if ( $this->session->get('filter') != '' )
        {
            $filter = $this->session->get('filter'); // continues below
        }
        $mylist = array(); // initialize array

        try
        {/* initialize variables */
            $match = '';
            $connection = $this->container->get('database_connection');

            if (isset($filter))
            {/* get columns in view */
                $temp = $connection->fetchAll("show columns from $customer.$this->view");
                foreach ($temp as $values) {
                    $viewcols[] = $values["Field"];
                }
            /*  get tablenames that shall be filtered */
                $tables = $connection->fetchAll("explain select * from $customer.$this->view");
                foreach ($tables as $values)
                {/* for each table in view */
                    $fulltext = $connection->fetchAll("show index from $customer.".$values["table"]);
                    foreach ($fulltext as $indices)
                    {/* get fulltext index fields */
                        if ($indices["Index_type"] == "FULLTEXT") {
                            if (in_array($indices["Column_name"], $viewcols)) {
                                $match .= $indices["Column_name"].", ";
                            }
                        }
                    }
                }
                $match = substr($match, 0, strlen($match)-2 );
                $match = " where match ($match) against ('".$filter."' in boolean mode)";
                $sql .= $match;
            }
            $mylist = $connection->fetchAll( $sql );
            if (count($mylist) == 0) {
                $this->session->set("errormsg",
                    "Leere Tabelle ".$this->view." keine Werte zum anzeigen (Filter?).");
            }
            return $mylist; // header information

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
        $sql = "select $this->pkey from $customer.$this->view limit 0, 2";
        $mylist = array(); // initialize array

        try {
            // make a database call to get the meta data
            $connection = $this->container->get('database_connection');
            $mylist = $connection->fetchAll( $sql );
            if (count($mylist) == 0) {
                $this->session->set("errormsg",
                    "Leere Tabelle ".$this->view." keine Werte zum anzeigen (Hinzufügen?).");
                return 0;
            } else {
                return $mylist[0][$this->pkey];
            }

        } catch (PDOException $e) {
            $this->session->set("errormsg","Cannot access database : ".$e);
            return 0;
        }
    }

    public function makeList()
    {/*
      * make a list (array) with data (headers, values) from the customer database
      * call this before rendering the list
      */
        $route = $this->session->get('route');
        if (!$this->session->has("cursor/$route"))
        {/* no cursor defined yet, get default */
            $this->session->set("cursor/$route", $this->getDefaultID());
        }
        /* set subject in the session */
        $this->session->set("subject", MenuTree::getCell($route, "CAPTION")." (Liste)");
        /* get list data (table) */
        $this->list = $this->getTable();
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