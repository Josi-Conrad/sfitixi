<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 08.10.13
 * Time: 10:30
 * initial file
 */
// martin jonasse 08.10.2013 dressed up the code interface

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class FormBuilder extends Controller
{/*
  * class for building forms automatically depending on the fields (columns) in the database
  */
    protected $container; // container awareness
    protected $view;      // input (name of the MySQL view)
    protected $pkey;      // input (name of the primary key)
    protected $meta;      // meta form data and values

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
        $this->meta = array();
    }

    public function setView($value)
    {/*
      * define the name of the view
      * the mysql database defines the data structure and constraints
      * please observe MySQL chapteer 18.4.3 Updateable amd Insertable Views
      */
        $this->view = $value;
    }

    public function setPkey($value)
    {/*
      * define the name of the primary key for the $view
      */
        $this->pkey = $value;
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
            $connection = $this->container->get('database_connection');
            $rownr = 0;
            $sql = "show full columns from $customer.$this->view";
            $this->meta = $connection->fetchAll( $sql );

            // update meta data
            foreach ($this->meta as $key => $value) {

                // readonly?
                $postfix = substr($value["Field"], -3 ); // last three characters
                $readonly = (($postfix == '_id') or ($value == 'MID')); // primary key and migration id
                $this->meta[$key]["Readonly"] = $readonly;

                // hidden?
                $hidden = ($postfix == '_fk');
                $this->meta[$key]["Hidden"] = $hidden;

                // maxlength?
                $this->meta[$key]["Length"] = $this->getLen($value["Type"]);

                // basetype?
                $paranthesis = strpos($value["Type"], "(");
                $basetype = ($paranthesis !== false) ? substr($value["Type"], 0, $paranthesis ) : $value["Type"];
                $this->meta[$key]["Basetype"] = $basetype;

                // map to (render as) html input type
                if       ($basetype == 'int')        { $this->meta[$key]["Rendered"] = 'text';
                } elseif ($basetype == 'varchar')    { $this->meta[$key]["Rendered"] = 'text';
                } elseif ($basetype == 'tinyint')    { $this->meta[$key]["Rendered"] = 'checkbox';
                } elseif ($basetype == 'mediumtext') { $this->meta[$key]["Rendered"] = 'textarea';
                } elseif ($basetype == 'text')       { $this->meta[$key]["Rendered"] = 'textarea';
                } else {                               $this->meta[$key]["Rendered"] = 'undefined';
                }

                // error message
                $this->meta[$key]["Error"] = ''; // empty
            }

            return $this->meta;

        } catch (PDOException $e) {
            $session->set("errormsg","Cannot access database : ".$e);
            return "0";
        }
    }

    public function getFormMeta()
    {/*
      * get the forms meta data, based upon the data in the (joined) table(s)
      * the submitted user data will be persisted to the database
      */
        $session = new session;
        $this->meta = $this->getMetaData($session);
        return $this->meta;
    }

    public function getFormData($cursor)
    {/*
      * return an array containing the persistent data values for the form
      */
        $session = new session;
        $customer = $session->get('customer');

        try {
            // make a database call to get the meta data
            $connection = $this->container->get('database_connection');
            $sql = "select * from $customer.$this->view where $this->pkey=$cursor";
            $data = $connection->fetchAll( $sql );
            // return an array with persistent data (query has only record)
            return $data[0];

        } catch (PDOException $e) {
            $session->set("errormsg","Cannot access database : ".$e);
            return "0";
        }

    }

}