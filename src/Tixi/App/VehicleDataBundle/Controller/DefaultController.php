<?php

/* src/Tixi/App/VehicleDataBundle/Controller/Default/DefaultController.php
 * 09.11.2013 martin jonasse initial file
 */

namespace Tixi\App\VehicleDataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;

class DefaultController extends Controller
{/*
  * controller for the vehicle data page
  */
    public function indexAction($name='')
    {
        /* initialize the context */
        $route = 'tixi_fahrzeug_page';
        $housekeeper = $this->get('tixi_housekeeper');
        if ($housekeeper->setTemplateParameters($route) != 0) {
            return $this->render('TixiHomeBundle:Default:error403.html.twig');
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateVehicleData")); // callback
        $autoform->setCollection(true);
        $autoform->setPkey("fahrzeug_id"); // name of primary key
        $autoform->setFormview("form_fahrzeug");
        $autoform->setListView("list_fahrzeug");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validateVehicleData($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * this may not be efficient, but at least it works well
      *
      * return: the modified myform array: Value and or Error or ...
      */
        foreach ($myform as $key => $values) {

            if ($values["Field"] == "inverkehrsetzung")
            {
                $iday = date_create($values["Value"]);
                $today = date_create(date("Y-m-d"));
                if ($iday >= $today) {
                    $myform[$key]["Error"] = "Validierungsfehler: Inverkehrsetzung muss in der Vergangenheit liegen.";
                }
            }
            elseif ($values["Field"] == "anzahl_sitze")
            {
                $i = $values["Value"];
                $max = 8;
                if (($i <= 0) or ($i > $max)) {
                    $myform[$key]["Error"] = "Validierungsfehler: Zahl im Bereich 1 bis $max erlaubt.";
                }
            }
            elseif ($values["Field"] == "anzahl_rollstuehle")
            {
                $i = $values["Value"];
                $max = 8;
                if (($i < 0) or ($i > $max)) {
                    $myform[$key]["Error"] = "Validierungsfehler: Zahl im Bereich 0 bis $max erlaubt.";
                }
            }
        }
        return $myform; // return the changed local copy of the myform array
    }
}
