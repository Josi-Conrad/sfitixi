<?php

/* src/Tixi/App/TeamBundle/Controller/Default/DefaultController.php
 * 28.08.2013 martin jonasse initial file
 * 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters, added $mode
 * 30.09.2013 martin jonasse implemented first version of StateBuilder
 * 28.10.2013 martin jonasse finished unit tests, after some fixing and fine tuning
 * 05.12.2013 martin jonasse upgraded password encryption to the official Symfony2 standard
 */

namespace Tixi\App\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;
use Tixi\UserBundle\Service\MyUser;

class DefaultController extends Controller
{/*
  * controller for the team page (manager only view)
  * see also: preferences page
  */
    public function indexAction($name='')
    {
        /* initialize the context */
        $route = 'tixi_unterhalt_teamdaten_page';
        $housekeeper = $this->get('tixi_housekeeper');
        if ($housekeeper->setTemplateParameters($route) != 0) {
            return $this->render('TixiHomeBundle:Default:error403.html.twig');
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallValidate(array($this, "validateTeamdata")); // callback
        $autoform->setCollection(true);
        $autoform->setPkey("benutzer_id"); // name of primary key
        $autoform->setFormview("form_team");
        $autoform->setListView("list_team");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function validateTeamdata($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * this may not be efficient, but at least it works well
      *
      * return: the modified myform array: Value and or Error or ...
      */
        foreach ($myform as $key => $values) {
            if ($values["Field"] == "passwort")
            {
                if (strlen($values["Value"]) < 8)
                {/* ensure minimal length of the password entered by the user */
                    $myform[$key]["Error"] =
                        "Validierungsfehler: Passwörter müssen mindestens 8 Zeichen lang sein.";
                } elseif ($values["Change"] == true)
                {/* password has been changed by user, hash password with salt in httpd.conf SetEnv APACHE_SALT */
                    $password = $values["Value"];
                    $salt = getenv("APACHE_SALT");
                    $password = hash("sha512", $password.'{'.$salt.'}'); // 128 characters
                    $myform[$key]["Value"] = $password;
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
            }
            elseif ($values["Field"] == "benutzername")
            {
                if (filter_var($values["Value"], FILTER_VALIDATE_EMAIL) == false) {
                    $myform[$key]["Error"] = "Validierungsfehler: Benutzername ungültig (Format: name@firma.ch).";
                }
            }
        }
        return $myform; // return the changed local copy of the myform array
    }
}
