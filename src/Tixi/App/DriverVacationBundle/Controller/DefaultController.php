<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 20.11.13
 * Time: 12:04
 */

namespace Tixi\App\DriverVacationBundle\Controller;

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
        $route = 'tixi_fahrer_ferienplan_page';
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
            return $this->redirect($this->generateUrl('tixi_fahrer_page'));
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallValidate(array($this, "validateDriverVacation"));
        $autoform->setCollection(true);
        $autoform->setPkey("ferienplan_id"); // name of primary key
        $autoform->setFormview("form_ferienplan");
        $autoform->setListView("list_ferienplan");
        $autoform->setConstraint("ferienplan_fahrer_fk = $parentid");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

        public function validateDriverVacation($myform=array())
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
                    $myform[$key]["Error"] = "Validierungsfehler: Ferien Ende muss nach Ferien Anfang liegen.";
                }
                elseif ($values["Value"]==""){
                    $myform[$key]["Value"] = NULL; // otherwise it's stored as 0000-00-00 00:00:00
                }
            }
        }

        return $myform; // return the changed local copy of the myform array
    }
}
