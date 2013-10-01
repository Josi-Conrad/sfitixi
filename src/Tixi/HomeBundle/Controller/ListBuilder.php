<?php
/**
 * Created by JetBrains PhpStorm.
 * User: martin jonasse
 * Date: 01.10.13
 * Time: 07:29
 * initial file
 */

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class ListBuilder extends Controller
{ /*
   * class for displaying a SQL view as a HTML table on the screen,
   * the user can select rows and / or filter these rows
   * inputs: the view and constraints, all other inputs are stored in the session
   * feature: the number of rows displayed is limited to 30
   */
    protected $container;   // container
    protected $view;        // inputs
    protected $constraints; // inputs
    protected $list;        // output

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setView($value) {
        $this->view = $value;
        $this->list = array();
    }

    public function setConstraints($value) {
        $this->constraints = $value;
        $this->list = array();
    }

    private function getTable($session)
    { /*
       * retrieve headers for the view from the customer database
       * return: array of headers (success) empty array and error message (failure)
       */
        $customer = $session->get('customer');
        $sql = "select * from $customer.$this->view";
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

    public function makeList()
    { /*
       * make a list (array) with data (headers, values) from the customer database
       */
        $session = new session;
        $this->list = $this->getTable($session);
     }

    public function getRows() {
        return $this->list;
    }

    public function getHeader() {
        if (count($this->list) == 0) {
            return array();
        } else {
            return array_keys($this->list[0]);
        }
    }
}