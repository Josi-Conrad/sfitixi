<?php

// src/Tixi/App/PreferencesBundle/Controller/DefaultController
// 28.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters, added $mode

namespace Tixi\App\PreferencesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\HouseKeeper;
use Tixi\HomeBundle\Controller\AutoForm;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{/*
  * controller for the user preference page
  * basically the same as in teamdata, but for one user (constraint)
  * with the exception of details (manager only, not in the view)
  *
  * see also: team data page
  */
    public function indexAction($name='')
    {/* initialize the context */
        $route = 'tixi_preferences_page';
        $housekeeper = $this->get('tixi_housekeeper');
        $housekeeper->setTemplateParameters($route);

        /*  get username */
        $session = $this->container->get('session');
        $username = $session->get('username');

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setFormview("form_benutzer"); // name of view
        $autoform->setPkey("benutzer_id"); // name of primary key
        $autoform->setCollection(false); // this is an individual object
        $autoform->setCallback(array($this, "validatePreferences")); // callback
        $autoform->setConstraint("benutzername = '".$username."'"); // sql expression

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validatePreferences($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * this may not be efficient, but at least it works well
      *
      * return: the modified myform array: Value and or Error or ...
      */
        foreach ($myform as $key => $values)
        {
            if ($values["Field"] == "passwort")
            {
                if (strlen($values["Value"]) < 8)
                {/* ensure minimal length of the password entered by the user */
                    $myform[$key]["Error"] =
                        "Validierungsfehler: Passwörter müssen mindestens 8 Zeichen lang sein.";
                } elseif ($values["Change"] == true)
                {/* password has been changed by user, hash password with salt in httpd.conf SetEnv APACHE_SALT */
                    $myform[$key]["Value"] = hash("sha256", $values["Value"].getenv("APACHE_SALT")); // 64 characters
                }
            }
            elseif ($values["Field"] == "benutzer_geburtstag")
            {
                if ($values["Value"] != "") {
                    $bday = date_create($values["Value"]);
                    $today = date_create(date("Y-m-d"));
                    if ($bday >= $today) {
                        $myform[$key]["Error"] = "Validierungsfehler: Geburtstag muss in der Vergangenheit liegen.";
                    }
                } else {
                    $myform[$key]["Value"] = NULL;
                }
            } elseif ($values["Field"] == "benutzername") {
                if ($values["Change"] == true)
                {
                    $myform[$key]["Error"] = "Validierungsfehler: Benutzername darf nicht geändert werden (Abbrechen).";
                }
            }
        }
        return $myform; // return the changed local copy of the myform array
    }
}
