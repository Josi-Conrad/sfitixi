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

define("TIXI_FORMTWIGFILE",'TixiHomeBundle:Default:form.html.twig' );
define("TIXI_LISTTWIGFILE",'TixiHomeBundle:Default:list.html.twig' );

class AutoForm extends Controller
{/*
  * all in one package using the form and list builder services
  * forms are generated automatically using MySQL metadata and data
  */
    protected $container;
    protected $session;

    protected $callback;          // callback function for validating the formdata
    protected $collection;        // true: collection of objects (many), false: one object
    protected $pkey;              // name of the primary key in both views
    protected $formview;          // the name of the MySQL view for the form
    protected $listview = "";     // the name of the MySQL view for the list
    protected $constraint = "";   // restriction(s) for subqueries (sql expression: foo = 'bar')
    protected $formtwig = TIXI_FORMTWIGFILE; // renders form
    protected $listtwig = TIXI_LISTTWIGFILE; // renders list

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
        $this->session = new session;
    }

    public function setCallback( $callback )
    {/* mandatory : the object name of the validation function */
        $this->callback = $callback;
    }

    public function setCollection( $collection )
    {/* mandatory : true: collection of objects (many), false: one object */
        $this->collection = $collection;
    }

    public function setPkey( $pkey )
    {/* mandatory : the name of the primary key in both views */
        $this->pkey = $pkey;
    }

    public function setFormview( $formview )
    {/* mandatory : the name of the MySQL view for the form */
        $this->formview = $formview;
    }

    public function setListview( $listview= "" )
    {/* mandatory : the name of the MySQL view for the list */
        $this->listview = $listview;
    }

    public function setConstraint( $constraint="" )
    {/* optional : empty or abc = def (sql expression: foo = 'bar') */
        $this->constraint = $constraint;
    }

    public function setFormtwig( $formtwig = TIXI_FORMTWIGFILE )
    {/* optional : the name of the twig file for rendering forms */
        $this->formtwig = $formtwig;
    }

    public function setListtwig( $listtwig = TIXI_LISTTWIGFILE )
    {/* optional : the name of the twig file for rendering lists */
        $this->listtwig = $listtwig;
    }

    private function checkAttributes()
    {/* test that all mandatory attributes have been set properly
      */
        if (isset($this->callback) and isset($this->formview) and isset($this->listview) and
            isset($this->pkey) and isset($this->collection) and isset ($this->constraint) and
            isset($this->formtwig) and isset($this->listtwig))
        {
                return true;
        } else
        {
            $this->session->set('errormsg',
                'Fehler in AutoForm, ein oder mehrere Attribute nicht gesetzt.');
            return false;
        }
    }

    public function makeAutoForm($route)
    {/* select, build, render form and persiste data to MySQL */
        if ($this->checkAttributes()==true)
        {/* its safe to continue */
            if ($this->collection == true)
            {/* code for managing a collection of objects */
                $this->session->set('errormsg',
                    'Fehler in AutoForm, collection noch nicht ausprogrammiert.'); // todo
            }
            else
            {/* code for managing a single object */
                // initialize formstatebuilder service
                $state = $this->get('tixi_formstatebuilder'); // start service
                $state->setFormView($this->formview);
                $state->setPkey($this->pkey);
                $state->setCallback($this->callback);
                $state->setCollection($this->collection); // false
                $state->setConstraint($this->constraint); // sql expression
                $state->makeIndividualObjectStates($route); // redundant to collection?
                // render form
                return $this->render($this->formtwig,
                    array('myform' => $state->getFormMetaData() ));
            }
        }
        else
        {/* render error page */
            return $this->render($this->formtwig,
                array('myform' => array() ));
        }
    }
}