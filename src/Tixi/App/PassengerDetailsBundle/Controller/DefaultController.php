<?php

namespace Tixi\App\PassengerDetailsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\HouseKeeper;
use Tixi\HomeBundle\Controller\AutoForm;
use Tixi\HomeBundle\Controller\MenuTree;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {/*
      * controller for confidential details for passengers
      */
        /* initialize the context */
        $route = 'tixi_fahrgast_details_page';
        $housekeeper = $this->get('tixi_housekeeper');
        $housekeeper->setTemplateParameters($route);

        /* get parent context */
        $parent = menutree::getCell($route, "PARENT");
        $session = $this->container->get('session');
        $parent_id = $session->get("cursor/$parent");
        if ($parent_id == null)
        {/* no parent active in this session, redirect to parent page */
            return $this->redirect($this->generateUrl('tixi_fahrgast_page'));
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateFahrgastDetails")); // callback
        $autoform->setCollection(false);
        $autoform->setPkey("fahrgast_details_fahrgast_fk"); // name of primary key
        $autoform->setFormview("form_fahrgast_details");
        $autoform->setConstraint("fahrgast_details_fahrgast_fk = $parent_id");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validateFahrerDetails($myform=array())
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
