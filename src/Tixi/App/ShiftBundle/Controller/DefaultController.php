<?php

namespace Tixi\App\ShiftBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;

class DefaultController extends Controller
{/*
  * controller for maintaining the shift times (begin and end)
  */

    public function indexAction()
    {
        /* initialize the context */
        $route = 'tixi_unterhalt_dienste_page';
        $housekeeper = $this->get('tixi_housekeeper');
        if ($housekeeper->setTemplateParameters($route) != 0) {
            return $this->render('TixiHomeBundle:Default:error403.html.twig');
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallValidate(array($this, "validateShift")); // callback
        $autoform->setCollection(true);
        $autoform->setPkey("dienst_id"); // name of primary key
        $autoform->setFormview("form_dienst");
        $autoform->setListView("list_dienst");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validateShift($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * this may not be efficient, but at least it works well
      *
      * return: the modified myform array: Value and or Error or ...
      */
        $tbegin = null;
        $tend = null;
        foreach ($myform as $key => $values) {

            if ($values["Field"] == "dienst_anfang")
            {
                $tbegin = date_create($values["Value"]);
                if ($values["Value"]=="") {
                    $myform[$key]["Value"] = NULL; // otherwise it's stored as 0000-00-00 00:00:00
                }
            }

            if ($values["Field"] == "dienst_ende")
            {
                $tend = date_create($values["Value"]);
                if ($tbegin > $tend) {
                    $myform[$key]["Error"] = "Validierungsfehler: Dienst Ende muss nach Dienst Anfang liegen.";
                }
                elseif ($values["Value"]==""){
                    $myform[$key]["Value"] = NULL; // otherwise it's stored as 0000-00-00 00:00:00
                }
            }
        }
        return $myform; // return the changed local copy of the myform array
    }
}
