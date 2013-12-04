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

    public function getUserData($username)
    {
        $session = new session;
        $customer = $session->get('customer');
        $sql = "select benutzer_id, benutzername, passwort, ist_manager, ist_disponent, ist_aktive ";
        $sql .= "from $customer.form_benutzer where benutzername ='".$username."'";

        try
        {
            $conn = $this->get('database_connection');
            $udata = $this->conn->fetchAll( $sql );
            return $udata; // success
        }
        catch (PDOException $e)
        {
            $session->set("errormsg","Fehler: Benutzerdaten nicht verfügbar ".$e);
            return false; // failed
        }
    }
}