<?php

namespace Tixi\App\PassengerAbsenceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;
use Tixi\HomeBundle\Controller\MenuTree;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {/*
      * controller for the driver vacation page
      */

        /* initialize the context */
        $route = 'tixi_fahrgast_abwesenheit_page';
        $housekeeper = $this->get('tixi_housekeeper');
        $housekeeper->setTemplateParameters($route);

        /* get parent context */
        $session = $this->container->get('session');
        $parent = menutree::getcell($route, "PARENT");
        $parentid = $session->get("cursor/$parent");
        if ($parentid == null)
        {/* no parent in this session, redirect to parent page */
            return $this->redirect($this->generateUrl('tixi_fahrgast_page'));
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validatePassengerAbsence"));
        $autoform->setCollection(true);
        $autoform->setPkey("abwesend_id"); // name of primary key
        $autoform->setFormview("form_abwesend");
        $autoform->setListView("list_abwesend");
        $autoform->setConstraint("abwesend_fahrgast_fk = $parentid");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validatePassengerAbsence($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * this may not be efficient, but at least it works well
      *
      * return: the modified myform array: Value and or Error or ...
      */

        $fbegin = null;
        $fend = null;
        foreach ($myform as $key => $values) {

            if ($values["Field"] == "von")
            {
                $fbegin = date_create($values["Value"]);
                if ($values["Value"]=="") {
                    $myform[$key]["Value"] = NULL; // otherwise it's stored as 0000-00-00 00:00:00
                }
            }

            if ($values["Field"] == "bis")
            {
                $fend = date_create($values["Value"]);
                if ($fbegin > $fend) {
                    $myform[$key]["Error"] = "Validierungsfehler: Abwesenheit Ende muss nach Abwesenheit Anfang liegen.";
                }
                elseif ($values["Value"]==""){
                    $myform[$key]["Value"] = NULL; // otherwise it's stored as 0000-00-00 00:00:00
                }
            }
        }

        return $myform; // return the changed local copy of the myform array
    }

}
