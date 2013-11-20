<?php

namespace Tixi\App\OviDatenBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {/*
      * controller for the points of interest page
      */
        /* initialize the context */
        $route = 'tixi_ovi_page';
        $housekeeper = $this->get('tixi_housekeeper');
        $housekeeper->setTemplateParameters($route);

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateOvidata")); // callback
        $autoform->setCollection(true);
        $autoform->setPkey("ovi_id"); // name of primary key
        $autoform->setFormview("form_ovi");
        $autoform->setListView("list_ovi");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validateOvidata($myform=array())
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
