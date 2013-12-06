<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 04.12.13
 * Time: 13:45
 * To change this template use File | Settings | File Templates.
 */

namespace Tixi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;

class UserData extends Controller
{/*
  * Service for retrieving user authentication data from the SQL database
  */
    protected $container;       // container

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function getCustomer($username)
    {/*
      * extract customer from username name@btb.ch
      */
        if (filter_var($username, FILTER_VALIDATE_EMAIL) == true)
        {
            $adr = explode("@", $username);
            if (count($adr) == 2){
                $dom = explode(".", $adr[1]);
                if (count($dom) == 2){
                    return $dom[0]; // success
                }
            }

        }
        return null; // failed
    }

    public function getUserData($username)
    {
        $customer = $this->getCustomer($username);
        $sql = "select * from $customer.form_team where benutzername ='".$username."'";

        try
        {
            $conn = $this->get('database_connection');
            $udata = $conn->fetchAll( $sql );
            return $udata; // success
        }
        catch (PDOException $e)
        {
            $session->set("errormsg","Fehler: Benutzerdaten nicht verfügbar ".$e);
            return false; // failed
        }
    }
}