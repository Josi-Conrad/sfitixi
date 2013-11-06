<?php

// src/Tixi/App/TeamBundle/Resources/views/Default/index.html.twig
// 28.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters, added $mode
// 30.09.2013 martin jonasse implemented first version of StateBuilder
// 28.10.2013 martin jonasse finished unit tests, after some fixing and fine tuning

namespace Tixi\App\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;

class DefaultController extends Controller
{/*
  * controller for the team page (manager only view)
  * see also: preferences page
  */
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
                    $myform[$key]["Value"] = hash("sha256", $values["Value"].getenv("APACHE_SALT")); // 64 characters
                }
            }
            elseif ($values["Field"] == "geburtstag")
            {
                $bday = date_create($values["Value"]);
                $today = date_create(date("Y-m-d"));
                if ($bday >= $today) {
                    $myform[$key]["Error"] = "Validierungsfehler: Geburtstag muss in der Vergangenheit liegen.";
                }
            }
            elseif ($values["Field"] == "benutzername")
            {
                $barr = explode("@", $values["Value"]);
                if (count($barr) != 2) {
                    $myform[$key]["Error"] = "Validierungsfehler: kein Email Format (benutzer@firma.ch).";
                } else {
                    $firma = explode(".", $barr[1]);
                    if (count($firma) != 2) {
                        $myform[$key]["Error"] = "Validierungsfehler: kein Email Format (benutzer@firma.ch).";
                    } else {
                        $session = new Session;
                        $customer = $session->get('customer');
                        if ($customer != $firma[0]) {
                            $myform[$key]["Error"] = "Validierungsfehler: falsche Firmaname ($customer erwartet).";
                        }
                    }
                }
            }
        }
        return $myform; // return the changed local copy of the myform array
    }

    public function indexAction($name='')
    {
    // set local variables
        $route = 'tixi_unterhalt_teamdaten_page';
        $pkey = 'benutzer_id';
        $session = $this->container->get('session');
        $tixi = $this->container->getParameter('tixi');

    // set parameters for the rendering of this page
        $tixi_housekeeper = $this->get('tixi_housekeeper');
        $tixi_housekeeper->setTemplateParameters($route);

    // set states according to actions
        $state = $this->get('tixi_formstatebuilder'); // start service
        $state->setFormView('form_benutzer_person');
        $state->setPkey($pkey);
        $state->setCallback(array($this, "validateTeamdata"));
        $state->makeCollectionObjectStates($route);

    // rendering options
        if ($session->get('mode') == $tixi["mode_select_list"])
        {/*
          * display a list of team members
          */
            $list = $this->get('tixi_listbuilder'); // start service
            $list->setListView('list_benutzer_person');
            $list->setPkey($pkey);
            $list->makeList();
            // render list
            return $this->render('TixiHomeBundle:Default:list.html.twig',
                           array('myheader' => $list->getHeader(),
                                 'myrows' => $list->getRows() ));

        } elseif ($session->get('mode') == $tixi["mode_edit_in_list"])
        {/*
          * display a form for the selected team member
          */
            return $this->render('TixiHomeBundle:Default:form.html.twig',
                           array('myform' => $state->getFormMetaData() ));

        } else
        {// unexpected state encountered
            $session->set('errormsg', "Unerwartete Zustand $session->get('mode') in Seite $route.");
            return $this->render('TixiHomeBundle:Default:form.html.twig' );
        }
    }
}
