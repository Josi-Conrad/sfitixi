<?php

namespace Tixi\App\HolidayBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;

class DefaultController extends Controller
{/*
  * controller for maintaining the official holidays
  */

    public function indexAction()
    {
        /* initialize the context */
        $route = 'tixi_unterhalt_feiertage_page';
        $housekeeper = $this->get('tixi_housekeeper');
        if ($housekeeper->setTemplateParameters($route) != 0) {
            return $this->render('TixiHomeBundle:Default:error403.html.twig');
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateHoliday")); // callback
        $autoform->setCollection(true);
        $autoform->setPkey("feiertag_id"); // name of primary key
        $autoform->setFormview("form_feiertag");
        $autoform->setListView("list_feiertag");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validateHoliday($myform=array())
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