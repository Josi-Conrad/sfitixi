<?php

namespace Tixi\App\OviDetailsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        /* initialize the context */
        $route = 'tixi_ovi_details_page';
        $housekeeper = $this->get('tixi_housekeeper');
        $housekeeper->setTemplateParameters($route);

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateOvidetails")); // callback
        $autoform->setCollection(false);
        $autoform->setPkey("ovi_details_ovi_fk"); // name of primary key
        $autoform->setFormview("form_ovi_details");

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
