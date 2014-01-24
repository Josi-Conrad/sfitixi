<?php

namespace Tixi\App\DriverRecurringTaskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;
use Tixi\HomeBundle\Controller\DataMiner;
use Tixi\HomeBundle\Controller\MenuTree;

class DefaultController extends Controller
{
    protected $parentid;
    protected $session;
    protected $shifts;
    protected $dataminer;
    protected $myweek = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag');

    private function getChildren()
    {/*
      * read all exisiting day - period -shift triplets for driver-id = parent_id from the database
      */
        $customer = $this->session->get("customer");
        $route = $this->session->get('route');
        $cursor = $this->session->get("cursor/$route"); // cursor in einsatzplan (parent)
        $sql = "select * from ".$customer.".dauereinsatz where dauereinsatz_dauereinsatzplan_fk = ".$cursor;
        $in = $this->dataminer->readData($sql);
        $out = array();
        foreach ($in as $key => $values){
            $out[$key]['task_day'] = $values['dauereinsatz_tag']; // Montag, Dienstag ...
            $out[$key]['task_period'] = $values['dauereinsatz_periode']; // 0, 1, 2, 3 ...
            $out[$key]['task_shift'] = $this->dataminer->getShiftName($values['dauereinsatz_dienst_fk']); // Schicht 1
        }
        return $out;
    }

    private function storeChildren()
    {/*
      * add day - period -shift triplets for driver-id = parent_id to the database
      * request data format: key => data
      * key = recur#-#
      *       dienst  6 characters
      *       #     number 0 .. 5 (period)
      *       -     minus sign
      *       #     number 0 (Mo), 1 (Di) .. 6 (So)
      * value = Schicht 1, Schicht 2 ...
      */
        $route = $this->session->get('route');
        $cursor = $this->session->get("cursor/$route"); // cursor in dauereinsatzplan (parent)
        $triplets = array();

        foreach ($_REQUEST as $key => $value)
        {/* prepare records using request data */
            if ((strpos($key, "dienst")!==false) and (strlen($key)==9))
            {/* validate request values
              */
                $period = substr($key, 6, 1); // 0 .. 5
                $vper = (($period >= 0) and ($period <= 5)) ? $period : 0;

                $daynumb = substr($key, 8, 1); // 0 (Mo) .. 6 (So)
                $vday = (array_key_exists($daynumb, $this->myweek)) ? $this->myweek[$daynumb] : 'Error';

                $triplets[] = array(
                    "dauereinsatz_id" => "DEFAULT",
                    "dauereinsatz_dauereinsatzplan_fk" => $cursor,
                    "dauereinsatz_tag" => "'".$vday."'",
                    "dauereinsatz_periode" => $vper,
                    "dauereinsatz_dienst_fk" => $this->dataminer->getShiftId($value));
            }
        }

        $customer = $this->session->get("customer");
        return $this->dataminer->insertData($triplets, "$customer.dauereinsatz");
    }

    private function deleteChildren()
    {/*
      * delete all preexisitng day - period - shift triplets for driver-id = parent_id from the database
      */
        $customer = $this->session->get("customer");
        $route = $this->session->get('route');
        $cursor = $this->session->get("cursor/$route"); // cursor in dauereinsatzplan (parent)
        $sql = "delete from ".$customer.".dauereinsatz where dauereinsatz_dauereinsatzplan_fk = ".$cursor;
        return $this->dataminer->execData($sql);
    }

    private function convertRecordToHtml($myrecords)
    {/*
      * convert child record to html: garbagecan icon, input text fields with day, offset and radio buttons
      * input: array with zero, one or many records (same as in function getChildren)
      *        task_day = "Monday"
      *        task_period = 0
      *        task_shift = "Schicht 1"
      */
        $mysubform = array();
        foreach ($myrecords as $key => $values)
        {
            $daynum = 0;
            foreach ($this->myweek as $off => $value)
            {   if ($value == $values['task_day'])
                {   $daynum = $off;
                    break;
                }
            }
            $shortid = $values['task_period']."-".$daynum;
            if ($values['task_period'] == 0)
            {
                $period = 'jede Woche';
            } else
            {
                $period = $values['task_period'].". Woche";
            }
            $mysubform[$key] =
                "\n<p id=\"task$shortid\">".
                "\n<img src=\"/sfitixi/web/images/trashcan.gif\" ".
                        "alt=\"Löschen\" onclick=\"deleteElement('task$shortid'); refreshWDP();\"/> ".
                "\n<input type=\"text\" class=\"taskdate\" name=\"dienst".$shortid.
                        "\" value=\"".$values['task_day']." (".$period.")\" disabled>";
            $shifts = "";
            foreach ($this->shifts as $shift)
            {/* <input type="radio" name ="dienst?" value="Schicht 1" title="09:00 - 13:00" checked >Schicht 1 */
                $check = ($shift['dienst_name'] == $values['task_shift']) ? "checked" : "";
                $shifts .= "\n<input type=\"radio\" name =\"dienst$shortid\" value =\"".$shift['dienst_name']."\" ".
                    "title =\"".substr($shift['dienst_anfang'], 0, -3)." - ".substr($shift['dienst_ende'], 0, -3)."\" ".
                    $check.">".$shift['dienst_name'];
            }
            $mysubform[$key] .= $shifts."\n</p>";
        }
        return $mysubform;
    }

    public function indexAction()
    {/*
      * controller for the driver schedule page
      */
        /* initialize the context */
        $route = 'tixi_fahrer_dauereinsatz_page';
        $housekeeper = $this->get('tixi_housekeeper');
        if ($housekeeper->setTemplateParameters($route) != 0)
        {   return $this->render('TixiHomeBundle:Default:error403.html.twig');
        }
        /* get parent context */
        $this->session = $this->container->get('session');
        $parent = menutree::getcell($route, "PARENT");
        $this->parentid = $this->session->get("cursor/$parent");
        if ($this->parentid == null)
        {   return $this->redirect($this->generateUrl('tixi_fahrer_page'));
        }
        /* get array with shifts */
        $this->dataminer = $this->container->get('tixi_dataminer');
        if ($this->session->has('shifts'))
        {   $this->shifts = $this->session->get('shifts');
        }
        else
        {   $this->dataminer->makeShifts(); // add shifts to session
        }
        /*  start services */
        $autoform = $this->get('tixi_autoform'); // service name
        /* set attributes */
        $autoform->setCallValidate(array($this, "validateDriverRecurringTask"));
        $autoform->setCallSubform(array($this, "manageSubformData"));
        $autoform->setCollection(true);
        $autoform->setPkey("dauereinsatzplan_id"); // name of primary key
        $autoform->setFormview("form_dauereinsatzplan");
        $autoform->setListView("list_dauereinsatzplan");
        $autoform->setConstraint("dauereinsatzplan_fahrer_fk = $this->parentid");
        $autoform->setFormtwig("TixiAppDriverRecurringTaskBundle:Default:form.html.twig");

        /*  render form */
        return $autoform->makeAutoForm($route);
    }

    public function manageSubformData($mysubform=array())
    {/*
      * this function is called by the FormBuilder before the form is rendered
      * if applicable, this function returns an array with the subform data
      * return an empty array when no data is stored in the database
      * Please note:
      *   dependancy between parent (main form) and child data (in subform)
      */
        $action = ($this->session->get('action'));
        switch ($action) {
            case 'add':
                /* parent has been added, it cannot have children at this point in time, do nothing */
                break;

            case 'modify':
                /* parent has been read from database, now read children and return these as an array */
                $mysubform = $this->convertRecordToHtml($this->getChildren());
                return $mysubform;

            case 'save':
                /* saved parent, now save children (in $_REQUEST) to database */
                $this->deleteChildren();
                $this->storeChildren();
                break;

            case 'delete':
                /* delete children from the database, before the parent (observe referential integrity) */
                $this->deleteChildren();
                break;

            default:
                /* something very wrong happened in this applications code, please fix */
                $this->session->set('errormsg', "Subform: illegal action ($action) detected.");
                break;
        }
        return $mysubform;
    }

    public function validateDriverRecurringTask($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * this may not be efficient, but at least it works well
      *
      * return: the modified myform array: Value and or Error or ...
      */
        $error = false;
        foreach ($myform as $key => $values) {
            if ($values["Field"] == "dauereinsatzplan_betreff")
            {
                if ($values["Value"] == "")
                {
                    $myform[$key]["Error"] = "Validierungsfehler: der Betreff darf nicht leer sein.";
                    $error = true;
                }
            }
            else
            {
                if ($values["Error"] != "")
                {
                    $error = true;
                }
            }
        }
        return $myform; // return the changed local copy of the myform array
    }

}
