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
  * all in one package using both form and list builder services
  * forms are generated automatically using MySQL metadata and data
  * the metadata is cached in the session, ready to be reused
  */
    protected $container;
    protected $session;

    protected $callvalidate;          // callback function for validating the formdata
    protected $callsubform = array(); // callback for getting a subform data collection (optional)
    protected $collection;            // true: collection of objects (many), false: one object
    protected $pkey;                  // name of the primary key in both views
    protected $formview;              // the name of the MySQL view for the form
    protected $listview = "";         // the name of the MySQL view for the list
    protected $constraint = "";       // restriction(s) for subqueries
                                      // empty or a sql expression: foo = 'bar'

    protected $formtwig = TIXI_FORMTWIGFILE; // renders form
    protected $listtwig = TIXI_LISTTWIGFILE; // renders list

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
        $this->session = new session;
    }

    public function setCallValidate( $value )
    {/* mandatory : the object name of the validation function */
        $this->callvalidate = $value;
    }

    public function setCallSubform( $value )
    {/* optional : the object name of the subform function */
        $this->callsubform = $value;
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
    {/* optional : the name of the MySQL view for the list */
        $this->listview = $listview;
    }

    public function setConstraint( $constraint="" )
    {/* optional : empty or abc = 'def' (sql expression: foo = 'bar') */
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
    {/* test that all attributes have been set properly
      */
        if (isset($this->callvalidate) and isset($this->formview) and isset($this->listview) and
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
    {/* select, build, render form and persist data to MySQL database */

        $tixi = $this->container->getParameter('tixi');

        if ($this->checkAttributes()==true)
        {/* it's safe to continue, initialize formstatebuilder service */
            $state = $this->get('tixi_formstatebuilder'); // start service
            $state->setFormView($this->formview);
            $state->setListView($this->listview);
            $state->setPkey($this->pkey);
            $state->setCallValidate($this->callvalidate);
            $state->setCollection($this->collection);
            $state->setConstraint($this->constraint);
            $state->setCallSubform($this->callsubform);

            if ($this->collection == true)
            {/* code for managing a collection of objects */

                $state->makeCollectionObjectStates($route);

                $mode = $this->session->get('mode');
                if ($mode == $tixi["mode_select_list"])
                {/* display a list of objects */
                    $list = $this->get('tixi_listbuilder'); // start service
                    $list->setListView($this->listview);
                    $list->setPkey($this->pkey);
                    $list->setConstraint($this->constraint);
                    $list->makeList($route);
                    return $this->render($this->listtwig,
                                         array('myheader' => $list->getHeader(),
                                               'myrows' => $list->getRows() ));

                } elseif ($mode == $tixi["mode_edit_in_list"])
                {/* display a form for the selected object */
                    return $this->render($this->formtwig,
                                         array('myform' => $state->getFormMetaData(),
                                               'mysubform' => $state->getSubform() ));

                } else
                {/* unexpected state encountered */
                    $this->session->set('errormsg',
                        "Unerwartete Zustand $mode in Seite $route.");
                    return $this->render($this->formtwig,
                                         array('myform' => array(),
                                               'mysubform' => array() ));
                }
            }
            else
            {/* code for managing a single object */
                $state->makeIndividualObjectStates($route);
                return $this->render($this->formtwig,
                                     array('myform' => $state->getFormMetaData(),
                                           'mysubform' => $state->getSubform() ));
            }
        }
        else
        {/* render error page */
            return $this->render($this->formtwig, array('myform' => array(), 'mysubform' => array() ));
        }
    }
}