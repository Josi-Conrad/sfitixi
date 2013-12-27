<?php

namespace Tixi\App\VehicleDetailsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\HouseKeeper;
use Tixi\HomeBundle\Controller\AutoForm;
use Tixi\HomeBundle\Controller\MenuTree;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {/*
      * controller for confidential details for vehicles
      */
        /* initialize the context */
        $route = 'tixi_fahrzeug_details_page';
        $housekeeper = $this->get('tixi_housekeeper');
        if ($housekeeper->setTemplateParameters($route) != 0) {
            return $this->render('TixiHomeBundle:Default:error403.html.twig');
        }

        /* get parent context */
        $parent = menutree::getCell($route, "PARENT");
        $session = $this->container->get('session');
        $parent_id = $session->get("cursor/$parent");
        if ($parent_id == null)
        {/* no parent active in this session, redirect to parent page */
            return $this->redirect($this->generateUrl('tixi_fahrzeug_page'));
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateVehicleDetails")); // callback
        $autoform->setCollection(false);
        $autoform->setPkey("fahrzeug_id"); // name of primary key
        $autoform->setFormview("form_fahrzeug_details");
        $autoform->setConstraint("fahrzeug_id = $parent_id");

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
