<?php

namespace Tixi\App\OviDetailsBundle\Controller;

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
      * controller for confidential details for points of interest
      */
        /* initialize the context */
        $route = 'tixi_ovi_details_page';
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
            return $this->redirect($this->generateUrl('tixi_ovi_page'));
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallValidate(array($this, "validateOvidetails")); // callback
        $autoform->setCollection(false);
        $autoform->setPkey("ovi_id"); // name of primary key
        $autoform->setFormview("form_ovi_details");
        $autoform->setConstraint("ovi_id = $parent_id");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validateOvidetails($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * this may not be efficient, but at least it works well
      *
      * return: the modified myform array: Value and or Error or ...
      */
        foreach ($myform as $key => $values) {

        }
        return $myform; // return the changed local copy of the myform array
    }

}
