<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martin jonasse
 * Date: 01.10.13
 * Time: 07:29
 * initial file
 */
// src\Tixi\HomeBundle\Controller\ListBuilder.php
// martin jonasse 03.10.2013 upgrade code w. uppercase, cursors, filters

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ListBuilder extends Controller
{ /*
   * class for displaying a SQL view as a HTML table on the screen,
   * the user can select rows and / or filter these rows
   * inputs: the view and all other inputs are stored in the session
   * feature: the number of rows displayed is limited to 30
   */
    protected $container;   // container
    protected $route;       // route (index for cursors)
    protected $view;        // inputs (name of MySQL view)
    protected $list;        // output

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setRoute($value)
    {/*
      * route name which references the ROUTE column in the menutree table
      */
        $this->route = $value;
        $this->list = array();
    }

    public function setView($value)
    {/*
      * view name, must match an existing MySQL view in the customer database
      */
        $this->view = $value;
        $this->list = array();
    }

    private function getTable($session)
    {/*
      * retrieve headers for the view from the customer database
      * return: array of headers (success) empty array and error message (failure)
      */
        $customer = $session->get('customer');
        $sql = "select * from $customer.$this->view";
        if ($session->get('filter') != $session->get('const_filter'))
        {
            $sql .= " where benutzername like '%".$session->get('filter')."%'";
            $sql .= " or anrede like '%".$session->get('filter')."%'";
        }
        $mylist = array(); // initialize array

        try {
            // make a database call to get the meta data
            $connection = $this->container->get('database_connection');
            $mylist = $connection->fetchAll( $sql );
            if (count($mylist) == 0) {
                $session->set("errormsg","Leere Tabelle ".$this->view." keine Werte zum anzeigen.");
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
        $sql = "select id from $customer.$this->view";
        $mylist = array(); // initialize array

        try {
            // make a database call to get the meta data
            $connection = $this->container->get('database_connection');
            $mylist = $connection->fetchAll( $sql );
            if (count($mylist) == 0) {
                $session->set("errormsg","Leere Tabelle ".$this->view." keine Werte zum anzeigen.");
                return "0";
            } else {
                return $mylist[0]["id"];
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
        if (!isset($cursors[$this->route])) {
            $cursors[$this->route] = $this->getDefaultID($session);
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
      * return an array containing the (uupercase) header data of the database query,
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