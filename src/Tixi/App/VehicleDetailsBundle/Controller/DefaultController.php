<?php

/* src/Tixi/App/VehicleDetailsBundle/Controller/Default/DefaultController.php
 * 09.11.2013 martin jonasse initial file
 */

namespace Tixi\App\VehicleDetailsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;
use Tixi\HomeBundle\Controller\MenuTree;

class DefaultController extends Controller
{/*
  * controller for the vehicle details page
  */
    public function indexAction($name='')
    {
        /* initialize the context */
        $route = 'tixi_fahrzeug_details_page';
        $housekeeper = $this->get('tixi_housekeeper');
        $housekeeper->setTemplateParameters($route);

        /* get parent context */
        $session = $this->container->get('session');
        $parent = menutree::getcell($route, "PARENT");
        $vehicle_id = $session->get("cursor/$parent");
        if ($vehicle_id == null)
        {/* no parent in this session, redirect to parent page */
            return $this->redirect($this->generateUrl('tixi_fahrzeug_page'));
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateVehicleDetails"));
        $autoform->setCollection(false);
        $autoform->setPkey("fahrzeug_fk"); // name of primary key
        $autoform->setFormview("form_fahrzeug_details");
        $autoform->setConstraint("fahrzeug_fk = $vehicle_id");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validateVehicleDetails($myform=array())
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
