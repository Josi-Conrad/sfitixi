<?php

namespace Tixi\App\DriverDataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {/*
      * controller for the driver data page
      */
        /* initialize the context */
        $route = 'tixi_fahrer_page';
        $housekeeper = $this->get('tixi_housekeeper');
        $housekeeper->setTemplateParameters($route);

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateDriverData")); // callback
        $autoform->setCollection(true);
        $autoform->setPkey("fahrer_id"); // name of primary key
        $autoform->setFormview("form_fahrer");
        $autoform->setListView("list_fahrer");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    private function check_vehicle_type($value)
    {/*
      * check and see if the fahrzeug_type field matches a fahrzeugname in table fahrzeug
      * return true: success, false: failed
      */
        // todo continue here (20.11.2013)
        return false;
    }

    public function validateDriverData($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * this may not be efficient, but at least it works well
      *
      * return: the modified myform array: Value and or Error or ...
      */
        foreach ($myform as $key => $values) {

            if (($values["Field"] == "geburtsdatum") and ($values["Value"] != ""))
            {
                $bday = date_create($values["Value"]);
                $today = date_create(date("Y-m-d"));
                if ($bday >= $today) {
                    $myform[$key]["Error"] = "Validierungsfehler: Geburtstag muss in der Vergangenheit liegen.";
                }
            }

            if (($values["Field"] == "emailadresse") and ($values["Value"] != ""))
            {
                if (filter_var($values["Value"], FILTER_VALIDATE_EMAIL) == false) {
                    $myform[$key]["Error"] = "Validierungsfehler: Formatierungsfehler im Email Adresse festgestellt.";
                }
            }

            if (($values["Field"] == "fahrzeug_type") and ($values["Value"] != "*"))
            {/* the asterik designates "all", meaning no restrictions */
                $cartype = $values["Value"];
                if ($this->check_vehicle_type($cartype) == false){
                    $myform[$key]["Error"] = "Validierungsfehler: Fahrzeug Type $cartype nicht in Tabelle Fahrzeug gefunden.";
                }
            }

        }
        return $myform; // return the changed local copy of the myform array
    }
}
