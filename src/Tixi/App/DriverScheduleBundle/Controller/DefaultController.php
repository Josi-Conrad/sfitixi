<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 07.01.2014
 * Time: 09:35
 */

namespace Tixi\App\DriverScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;
use Tixi\HomeBundle\Controller\MenuTree;

class DefaultController extends Controller
{
    protected $parentid;
    protected $session;

    public function indexAction($name='')
    {/*
      * controller for the driver schedule page
      */

        /* initialize the context */
        $route = 'tixi_fahrer_einsatzplan_page';
        $housekeeper = $this->get('tixi_housekeeper');
        if ($housekeeper->setTemplateParameters($route) != 0) {
            return $this->render('TixiHomeBundle:Default:error403.html.twig');
        }

        /* get parent context */
        $this->session = $this->container->get('session');
        $parent = menutree::getcell($route, "PARENT");
        $this->parentid = $this->session->get("cursor/$parent");
        if ($this->parentid == null)
        {/* no parent in this session, redirect to parent page */
            return $this->redirect($this->generateUrl('tixi_fahrer_page'));
        }

        /*  start service */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallback(array($this, "validateDriverSchedule"));
        $autoform->setCollection(true);
        $autoform->setPkey("einsatzplan_id"); // name of primary key
        $autoform->setFormview("form_einsatzplan");
        $autoform->setListView("list_einsatzplan");
        $autoform->setConstraint("einsatzplan_fahrer_fk = $parentid");
        $autoform->setFormtwig("TixiAppDriverScheduleBundle:Default:form.html.twig");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    private function storeDateShiftPairs()
    {/*
      * delete all preexisitng date-shift pairs for driver with id = parent_id
      */
        $customer = $this->session->get("customer");
        $sql = "delete * from ".$customer.".einsatz where einsatz_einsatzplan_fk = ".$this->parentid;
        // todo: this is the interface to the database drivers ...
     /*
      * store all date-shift pairs in $_REQUEST to table einsatz
      */
        $dspairs = array();
        foreach ($_REQUEST as $key => $value)
        {
            if ((strpos($key, "date")!==false) and (strlen($key)==12))
            {
                $dskey = substr($key, 4);
                $dspairs[$dskey]["date"] = $value;
            }
            elseif ((strpos($key, "dienst")!==false) and (strlen($key)==14))
            {
                $dskey = substr($key, 6);
                $dspairs[$dskey]["dienst"] = $value;
            }
        }
        foreach ($dspairs as $values)
        {
            $task_shift = $values["dienst"]; // convert to dienst_id
            $task_date = $values["date"];    // convert to ISO date (yyyy-mm-dd)
            // todo: persist to database
        }
        $dummy = 'stop';
    }

    public function validateDriverSchedule($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * this may not be efficient, but at least it works well
      *
      * return: the modified myform array: Value and or Error or ...
      */
        $error = false;
        foreach ($myform as $key => $values) {
            if ($values["Field"] == "einsatzplan_betreff")
            {/* test for empty value ... */
                if ($values["Value"] == "")
                {
                    $myform[$key]["Error"] = "Validierungsfehler: der Betreff darf nicht leer sein.";
                    $error = true;
                }
            }
            else
            {/* test for error message(s) set ...*/
                if ($values["Error"] != "") {
                    $error = true;
                }
            }
        }

        if ($error == false)
        {/* data can be written to database ... */
            $this->storeDateShiftPairs($myform);
        }

        return $myform; // return the changed local copy of the myform array
    }
}
