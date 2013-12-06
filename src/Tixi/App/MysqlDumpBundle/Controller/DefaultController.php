<?php

namespace Tixi\App\MysqlDumpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\HouseKeeper;
use Tixi\HomeBundle\Controller\MenuTree;

class DefaultController extends Controller
{/*
  * backup the customer database to a file on the host
  */
    public function indexAction($name='')
    {/*
      * controller for making backups of the customer database
      */
        $route = 'tixi_unterhalt_datenbank_backup_page';
        $housekeeper = $this->get('tixi_housekeeper');
        if ($housekeeper->setTemplateParameters($route) != 0) {
            return $this->render('TixiHomeBundle:Default:error403.html.twig');
        }

        /* set subject */
        $session = $this->container->get('session');
        $customer = $session->get('customer');
        $session->set('subject', menutree::getCell($route, "CAPTION")." : $customer" );

        $bkpdir = $this->container->getParameter("tixi")["backup_path"].$customer;
        if ($session->get('action') == 'backup')
        {/* dump the database to the file system */
            $dbhost = $this->container->getParameter('database_host');
            $dbuser = $this->container->getParameter('database_user');
            $dbpass = $this->container->getParameter('database_password');
            if ($dbpass != ""){
                $dbpass = "-p ".$dbpass;
            }
            $bkpfile = $bkpdir."\\".$customer."_backup_".date("Ymd_His").".sql";
            $mysqldump = $this->container->getParameter('database_dump');
            $bkpcmd = "$mysqldump --opt -h $dbhost -u $dbuser $dbpass $customer > $bkpfile 2>&1";
            exec($bkpcmd);
        }
        /* display existing backups, the last one on top */
        $myfiles = array();
        if (file_exists($bkpdir)) {
            $myscan = scandir($bkpdir, 1);
            foreach ($myscan as $fname){
                if (($fname != ".") and ($fname != "..")) {
                    $fsize = filesize($bkpdir.'\\'.$fname);
                    $myfiles[] = array($fname, $fsize);
                }
            }
        }
        else {
            $session->set('errormsg', "Fehler in Filesystem: Ordner ".$bkpdir." nicht gefunden oder leer.");
        }

        return $this->render('TixiAppMysqlDumpBundle:Default:index.html.twig', array('myfiles' => $myfiles));
    }
}
