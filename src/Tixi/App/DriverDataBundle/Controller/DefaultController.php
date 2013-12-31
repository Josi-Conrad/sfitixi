<?php
/*
 * 30.12.2013 martin jonasse upgrade for check_vehicle_type
*/

namespace Tixi\App\DriverDataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\FormBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class DefaultController extends Controller
{
    public function indexAction()
    {/*
      * controller for the driver data page
      */
        /* initialize the context */
        $route = 'tixi_fahrer_page';
        $housekeeper = $this->get('tixi_housekeeper');
        if ($housekeeper->setTemplateParameters($route) != 0) {
            return $this->render('TixiHomeBundle:Default:error403.html.twig');
        }

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
      * wildcard (*) returns true, filter (xyz%) true if match(es) found in database
      * NOTE: basically the same function in PassengerDataBundle controller
      * return true: success, false: failed
      */
        if ($value == "*")
        {
            return true; // * is the wildcard
        }
        else
        {
            $connection = $this->container->get('database_connection');
            $session = new Session;
            $customer = $session->get('customer');
            $sql = "select fahrzeugname from $customer.fahrzeug where fahrzeugname like '".$value."'";
            try
            {
                $mylist = $connection->fetchAll( $sql );
                if (count($mylist)==0) {
                    return false; // $value not found in vehicle list
                }
                else {
                    return true; // $value found in vehicle list (count times)
                }
            }
            catch (PDOException $e)
            {
                $this->session->set("errormsg","Cannot access database : ".$e);
                return false;
            }
        }
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

            if ($values["Field"] == "fahrzeug_type")
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
