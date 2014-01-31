<?php
/* 15.11.2013 martin jonasse initial file */
/* 31.01.2014 martin jonasse added --routines to the dump options, this backs up functions and procedures */

namespace Tixi\App\MysqlDumpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\HouseKeeper;
use Tixi\HomeBundle\Controller\MenuTree;

class DefaultController extends Controller
{/*
  * backup the customer database to a file on the host
  */
    private function backupDatebase($dir, $db)
    {/* dump the database to the windows file system */
        $mysqldump = $this->container->getParameter('database_dump');
        $dbhost = $this->container->getParameter('database_host');
        $dbuser = $this->container->getParameter('database_user');
        $dbpass = $this->container->getParameter('database_password');
        if ($dbpass != ""){
            $dbpass = "-p ".$dbpass;
        }
        $dboptions = "--opt --single-transaction --routines";
        $dbnames = "--databases $db";
        $bkpfile = $dir."\\".$db."_backup_".date("Ymd_His").".sql";
        $bkpcmd = "$mysqldump -h $dbhost -u $dbuser $dbpass $dboptions $dbnames > $bkpfile";
        exec($bkpcmd);

     /* copy the itixi and btb database to the git repository */
        if (($db != 'btb') and ($db != 'itixi')) {
          return false;
        }
        $bkpcopy = $this->container->getParameter("tixi")["git_databkp"]."\\".$db."_backup.sql";
        if (!copy($bkpfile, $bkpcopy)){
            $session = $this->container->get('session');
            $session->set('errormsg', "Konnte Datei ($bkpfile) nicht kopieren.");
            return false; // failure
        }
        return true; // success
    }

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
        {/* dump the database to the windows file system */
            $this->backupDatebase($this->container->getParameter("tixi")["backup_path"]."itixi", "itixi");
            $this->backupDatebase($bkpdir, $customer);
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
