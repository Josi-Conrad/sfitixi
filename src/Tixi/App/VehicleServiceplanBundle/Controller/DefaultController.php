<?php

/* src/Tixi/App/VehicleDataBundle/Controller/Default/DefaultController.php
 * 09.11.2013 martin jonasse initial file
 */

namespace Tixi\App\VehicleServiceplanBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;
use Tixi\HomeBundle\Controller\MenuTree;

class DefaultController extends Controller
{/*
  * controller for the vehicle serviceplan page
  */
    public function indexAction($name='')
    {
        /* initialize the context */
        $route = 'tixi_fahrzeug_serviceplan_page';
        $housekeeper = $this->get('tixi_housekeeper');
        $housekeeper->setTemplateParameters($route);

        /* get parent context */
        $session = $this->container->get('session');
        $parent = menutree::getcell($route, "PARENT");
        $parentid = $session->get("cursor/$parent");
        if ($parentid == null)
        {/* no parent in this session, redirect to parent page */
            return $this->redirect($this->generateUrl('tixi_fahrzeug_page'));
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateVehicleServiceplan"));
        $autoform->setCollection(true);
        $autoform->setPkey("serviceplan_id"); // name of primary key
        $autoform->setFormview("form_serviceplan");
        $autoform->setListView("list_serviceplan");
        $autoform->setConstraint("fahrzeug_fk = $parentid");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validateVehicleServiceplan($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * this may not be efficient, but at least it works well
      *
      * return: the modified myform array: Value and or Error or ...
      */
        return $myform; // return the changed local copy of the myform array
    }
}
