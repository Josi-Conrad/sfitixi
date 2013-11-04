<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 31.10.13
 * Time: 17:38
 */

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\ListBuilder;
use Tixi\HomeBundle\Controller\FormBuilder;

class AutoForm extends Controller
{/*
 * all in one package using the form and list builder services
 * forms are generated automatically using MySQL:
 * * metadata and
 * * data (record from a view)
 */
    protected $container;

    protected $route;             // the name of the route
    protected $callback;          // callback function for validating the formdata
    protected $formview;          // the name of the MySQL view for the form
    protected $listview;          // the name of the MySQL view for the list
    protected $pkey;              // name of the primary key in both views
    protected $collection;        // true: collection of objects (many), false: one object
    protected $constraint = null; // restriction(s) for subqueries: array(key => value)
    protected $formtwig = 'TixiHomeBundle:Default:form.html.twig'; // renders form
    protected $listtwig = 'TixiHomeBundle:Default:list.html.twig'; // renders list

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setRoute( $route )
    {/* mandatory : the name of the route */
        $this->route = $route;
    }

    public function setCallback( $callback )
    {/* mandatory : the object name of the validation function */
        $this->callback = $callback;
    }

    public function setFormview( $formview )
    {/* mandatory : the name of the MySQL view for the form */
        $this->formview = $formview;
    }

    public function setListview( $listview )
    {/* mandatory : the name of the MySQL view for the list */
        $this->listview = $listview;
    }

    public function setPkey( $pkey )
    {/* mandatory : the name of the primary key in both views */
        $this->pkey = $pkey;
    }

    public function setCollection( $collection )
    {/* mandatory : true: collection of objects (many), false: one object */
        $this->collection = $collection;
    }

    public function setConstraint( $constraint=null )
    {/* optional : empty, in subqueries the foreign key => value (in an array) */
        $this->constraint = $constraint;
    }

    public function setFormtwig( $formtwig = 'TixiHomeBundle:Default:form.html.twig' )
    {/* optional : the name of the twig file for rendering forms */
        $this->formtwig = $formtwig;
    }

    public function setListtwig( $listtwig = 'TixiHomeBundle:Default:list.html.twig' )
    {/* optional : the name of the twig file for rendering lists */
        $this->listtwig = $listtwig;
    }

    public function checkAttributes($route)
    {/* test that all mandatory attributes have been set properly
      */
        if (isset($this->route) and isset($this->callback) and isset($this->formview) and
            isset($this->listview) and isset($this->pkey) and isset($this->collection)) {
            if ($this->route == $route) {
                return true;
            } else { /* route mismatch, not a reentrant service ? */
                $session = new session;
                $session->set('errormsg', "Fehler in AutoForm, ungleiche Attribute ($route, $this->route).");
                return false;
            }
        } else {
            $session = new session;
            $session->set('errormsg', 'Fehler in AutoForm, ein oder mehrere Attribute nicht gesetzt.');
            return false;
        }
    }

    public function makeAutoForm($route)
    {/* select, build, render form and persiste data to MySQL */
        if (checkAttributes($route)==true)
        {/* its safe to continue */
            // todo: continue here
        }
    }
}