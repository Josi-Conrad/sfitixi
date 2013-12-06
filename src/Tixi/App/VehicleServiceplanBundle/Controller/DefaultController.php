<?php

/* src/Tixi/App/VehicleDataBundle/Controller/Default/DefaultController.php
 * 09.11.2013 martin jonasse initial file
 */

namespace Tixi\App\VehicleServiceplanBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;
use Tixi\HomeBundle\Controller\MenuTree;

class DefaultController extends Controller
{/*
  * controller for the vehicle serviceplan page
  */
    public function indexAction($name='')
    {
        /* initialize the context */
        $route = 'tixi_fahrzeug_serviceplan_page';
        $housekeeper = $this->get('tixi_housekeeper');
        if ($housekeeper->setTemplateParameters($route) != 0) {
            return $this->render('TixiHomeBundle:Default:error403.html.twig');
        }

        /* get parent context */
        $session = $this->container->get('session');
        $parent = menutree::getcell($route, "PARENT");
        $parentid = $session->get("cursor/$parent");
        if ($parentid == null)
        {/* no parent in this session, redirect to parent page */
            return $this->redirect($this->generateUrl('tixi_fahrzeug_page'));
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateVehicleServiceplan"));
        $autoform->setCollection(true);
        $autoform->setPkey("serviceplan_id"); // name of primary key
        $autoform->setFormview("form_serviceplan");
        $autoform->setListView("list_serviceplan");
        $autoform->setConstraint("serviceplan_fahrzeug_fk = $parentid");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validateVehicleServiceplan($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * this may not be efficient, but at least it works well
      *
      * return: the modified myform array: Value and or Error or ...
      */

        $sbegin = null;
        $send = null;
        foreach ($myform as $key => $values) {
            if (($values["Field"] == "service_kosten") and ($values["Value"] != ""))
            {
                if (!is_numeric($values["Value"]))
                {
                    $myform[$key]["Error"] = "Validierungsfehler: Kosten sind leer oder ein Zahl.";
                }
                elseif ($values["Value"] < 0)
                {
                    $myform[$key]["Error"] = "Validierungsfehler: negative Kosten sind nicht erlaubt.";
                }
            }

            if ($values["Field"] == "service_anfang")
            {
                $sbegin = date_create($values["Value"]);
                if ($values["Value"]=="") {
                    $myform[$key]["Value"] = NULL; // otherwise it's stored as 0000-00-00 00:00:00
                }
            }

            if ($values["Field"] == "service_ende")
            {
                $send = date_create($values["Value"]);
                if ($sbegin > $send) {
                    $myform[$key]["Error"] = "Validierungsfehler: Service Ende muss nach Service Anfang liegen.";
                }
                elseif ($values["Value"]==""){
                    $myform[$key]["Value"] = NULL; // otherwise it's stored as 0000-00-00 00:00:00
                }
            }

        }

        return $myform; // return the changed local copy of the myform array
    }
}
