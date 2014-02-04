<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 07.01.2014
 * Time: 09:35
 */
/* 03.02.2014 martin jonasse changed shifts from radio button to checkbox */

namespace Tixi\App\DriverScheduleBundle\Controller;

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

    private function getChildren()
    {/*
      * read all exisiting date-shift pairs for driver-id = parent_id from the database
      */
        $customer = $this->session->get("customer");
        $route = $this->session->get('route');
        $cursor = $this->session->get("cursor/$route"); // cursor in einsatzplan (parent)
        $sql = "select * from ".$customer.".einsatz, ".$customer.".bereitschaft ";
        $sql .= "where einsatz_id = bereitschaft.bereitschaft_einsatz_fk ";
        $sql .= "and einsatz_einsatzplan_fk = ".$cursor;
        $in = $this->dataminer->readData($sql);
        $out = array();
        foreach ($in as $key => $values){
            $out[$key]['task_date'] = $this->dataminer->localizeDate($values['einsatz_datum']);
            $out[$key]['task_shift'] = $this->dataminer->getShiftName($values['bereitschaft_dienst_fk']);
        }
        return $out;
    }

    private function storeChildren()
    {/*
      * add date-children and shift-grandchildren for driver-id = parent_id to the database
      */
        $route = $this->session->get('route');
        $cursor = $this->session->get("cursor/$route"); // cursor in einsatzplan (parent)
        $customer = $this->session->get("customer");
        $isodate = '';

        foreach ($_REQUEST as $key => $value)
        {/* prepare records using request data */
            if ((strpos($key, "dienst")!==false) and (strlen($key)>14))
            {
                $dd = substr($key, 6, 2); // day
                $mm = substr($key, 8, 2); // month
                $yy = substr($key, 10, 4); // year
                $mydate = $yy.'-'.$mm.'-'.$dd;
                if ($mydate != $isodate)
                {/* insert task date into database */
                    $isodate = $mydate;
                    $myeinsatz = array(
                        "einsatz_id" => "DEFAULT",
                        "einsatz_einsatzplan_fk" => $cursor,
                        "einsatz_datum" => $isodate);
                    $id = $this->dataminer->insertData($myeinsatz, "$customer.einsatz" );
                }
                if ($id != 0)
                {/* insert task shift(s) into database */
                    $mybereitschaft = array(
                        "bereitschaft_id" => "DEFAULT",
                        "bereitschaft_einsatz_fk" => $id,
                        "bereitschaft_dienst_fk" => $this->dataminer->getShiftId($value) );
                    $this->dataminer->insertData($mybereitschaft, "$customer.bereitschaft" );
                }
            }
        }
    }

    private function deleteChildren()
    {/*
      * delete all preexisitng date-shift pairs for driver-id = parent_id from the database
      */
        $customer = $this->session->get("customer");
        $route = $this->session->get('route');
        $cursor = $this->session->get("cursor/$route"); // cursor in einsatzplan (parent)

        /* first delete grandchildren (shifts) */
        $sql = "delete from ".$customer.".bereitschaft where bereitschaft_einsatz_fk in ".
               "(select einsatz_id from ".$customer.".einsatz where einsatz.einsatz_einsatzplan_fk =".$cursor.")";
        $this->dataminer->execData($sql);

        /* then delete children (task dates) */
        $sql = "delete from ".$customer.".einsatz where einsatz_einsatzplan_fk = ".$cursor;
        $this->dataminer->execData($sql);

        return true;
    }

    private function convertRecordToHtml($myrecords)
    {/*
      * convert child record to html: garbagecan icon, input text field with date and checkboxes
      * input: array with zero, one or many records
      *        task_date = "07.02.2014"
      *        task_shift = "Schicht 1"
      */
        $mysubform = array();
        $mykey = -1;
        $mydate = '';
        foreach ($myrecords as $values)
        {
            if ($mydate != $values['task_date'])
            {/* new date, init string */
                $mydate = $values['task_date'];
                $mykey += 1;
                $shortid = str_replace(".", "", $mydate); // remove dots
                $mysubform[$mykey] =
                    "\n<p id=\"task$shortid\">".
                    "\n  <img src=\"/sfitixi/web/images/trashcan.gif\" ".
                    "         alt=\"Löschen\" onclick=\"deleteElement('task$shortid')\"/> ".
                    "\n  <input class=\"taskdate\" name=\"date$shortid\" type=\"text\" ".
                    "           value=\"".$values['task_date']."\" disabled>".
                    $this->session->get('shiftsHTML')."\n</p>";
                $mysubform[$mykey] = str_replace('?', $shortid, $mysubform[$mykey]);
            }
            /* set checked for each shift in $myrecords */
            $mysubform[$mykey] = str_replace('>'.$values['task_shift'],
                                             'checked>'.$values['task_shift'],
                                             $mysubform[$mykey]);
        }
        return $mysubform;
    }

    public function indexAction()
    {/*
      * controller for the driver schedule page
      */
        /* initialize the context */
        $route = 'tixi_fahrer_einsatzplan_page';
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
        $autoform->setCallValidate(array($this, "validateDriverSchedule"));
        $autoform->setCallSubform(array($this, "manageSubformData"));
        $autoform->setCollection(true);
        $autoform->setPkey("einsatzplan_id"); // name of primary key
        $autoform->setFormview("form_einsatzplan");
        $autoform->setListView("list_einsatzplan");
        $autoform->setConstraint("einsatzplan_fahrer_fk = $this->parentid");
        $autoform->setFormtwig("TixiAppDriverScheduleBundle:Default:form.html.twig");

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
