<?php

namespace Tixi\App\ZoneplanBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;

class DefaultController extends Controller
{/*
  * controller for maintaining the zone plan
  * zones are tarif zones, which define the price of the fare
  */
    public function indexAction()
    {
        /* initialize the context */
        $route = 'tixi_unterhalt_zonenplan_page';
        $housekeeper = $this->get('tixi_housekeeper');
        $housekeeper->setTemplateParameters($route);

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateZonenplan")); // callback
        $autoform->setCollection(true);
        $autoform->setPkey("zonenplan_id"); // name of primary key
        $autoform->setFormview("form_zonenplan");
        $autoform->setListView("list_zonenplan");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validateZonenplan($myform=array())
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
