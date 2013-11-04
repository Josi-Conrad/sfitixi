<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 21.08.13
 * Time: 16:09
 */
// src/Tixi/HomeBundle/Controller/HomepageController.php
// 26.08.2013 martin jonasse get username and customer from session
// 27.08.2013 martin jonasse added __construct
// 02.09.2013 martin jonasse added session variables and constants
// 03.09.2013 martin jonasse renamed getTemplateParameters to setTemplateParameters
// 06.09.2013 martin jonasse added automatic generation of menu.html.twig (improved performance)
// 21.09.2013 martin jonasse simplified setTemplateParameters, added $route, dropped all others
// 22.09.2013 martin jonasse $menutree is not persistent, set it as a Symfony2 Parameter
// 30.09.2013 martin jonasse updated management of actions and modes
// 01.10.2013 martin jonasse removed .ch from customer name
// 03.10.2013 martin jonasse upgrade cursor to array type
// 23.10.2013 martin jonasse added "tainted" to session
// 25.10.2013 martin jonasse added "breadcrumbs" to session (new menutree)
// 03.11.2013 martin jonasse added Menutree.php, fixed caption, permission and breadcrunmb
// 04.11.2013 martin jonasse added structured namespace for cursor session variables

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

define("TIXI_UNDEFINED", "undefined");
define("TWIG_MENUTREE", "/src/Tixi/HomeBundle/Resources/views/Default/menu.html.twig");
define("PHP_MENUTREE", "/src/Tixi/HomeBundle/Controller/MenuTree.php");

Class HouseKeeper extends Controller
{
    protected $container;       // container
    protected $initMenutree;    // boolean
    protected $menutree;        // array (not persistent)

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
        $this->initMenutree = true;
    }

    private function makeMenuHtmlTwig(Session $session)
    {   /**
         * build the twig file from the data in $menutree, which is a replica of table itixi.menutree.
         * the twig file is rebuilt after each build of the iTixi application (when initMenutree=true)
        */

        // prefix
        $mytext = '<div id="menu-bar">'."\n";
        $mytext .= '<ul class="nav">'."\n";

        // page variables
        $myurl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];
        $items = count($this->menutree);

        //loop
        foreach ($this->menutree as $key => $menuitem):
            if ($menuitem["LOCATION"] == 'menu-bar') {
            // this is a menu-bar item

                // calculate fully qualified anchor or placeholder anchor (w.o. href)
                $route = $myurl.$menuitem["URL"];
                $id = 'fpm'.str_replace('/', '_', $menuitem["URL"] );
                if ($menuitem["ENABLED"] == 0) {
                // placeholder anchor
                    $anchor = '<a id="'.$id.'">'.$menuitem["CAPTION"].'</a>';
                } else {
                // normal anchor
                    $anchor = '<a href="'.$route.'" id="'.$id.'">'.$menuitem["CAPTION"].'</a>';
                }

                // calculate level changes: down, same, up
                $thislevel = substr_count($menuitem["URL"],'/');
                if (($key + 1) >= $items) {
                    $nextlevel = 2;
                } else {
                    $nextlevel = substr_count($this->menutree[$key+1]["URL"],'/');
                }

               // build list elements
                if ($nextlevel == $thislevel) {
                    $mytext .= '<li>'.$anchor.'</li>'."\n"; // same level
                } elseif ($nextlevel > $thislevel) {
                    $mytext .= '<li class="dropdown">'.$anchor."\n"; // down level
                    $mytext .= '<ul>'."\n"; // down
                } elseif ($nextlevel < $thislevel) {
                    $mytext .= '<li>'.$anchor.'</li>'."\n"; // up level
                    for ($i = 1; $i <= ($thislevel - $nextlevel); $i++) {
                        $mytext .= '</ul></li>'."\n"; // up
                    };
                }
            }
        endforeach;

        // postfix
        $mytext .= '</ul></div>'."\n";

        return $mytext;
    }

    private function initMenuHtmlTwig(Session $session)
    {   /**
         * copy the twig file build to the file system.
         * this is faster than using the database (due to Symfony cache).
         */
        $this->get('logger')->debug('TIXI initializing mytextdumpfile.txt');

        // make new file
        $myfile = "{# ".TWIG_MENUTREE." #}\n";
        $myfile .= "{# built automatically by the iTixi application #}\n";
        $myfile .= $this->makeMenuHtmlTwig($session);

        // write new file to the filesystem as menu.html.twig
        $fs = new Filesystem();
        try {
            $fs->dumpFile( '..'.TWIG_MENUTREE, $myfile );
        } catch (IOException $e) {
            $session->set("errormsg","IO filesystem error: ".$e);
        }
    }

    private function makeMenutreePhp()
    {/*
      * build the file MenuTree.php content
      */
        // create header (php code)
        $header = <<<EOH
<?php
/**
 * Created automatically in HouseKeeper.php
 */

namespace Tixi\HomeBundle\Controller;

final class MenuTree
{
    public static
EOH;
        $header .= ' $table = array('."\n";

        // create footer (php code)
        $footer = str_repeat(" ", 4).');'."\n\n";
        $footer .= str_repeat(" ", 4).'public static function getRow($row){'."\n";
        $footer .= str_repeat(" ", 8).'if (array_key_exists($row, self::$table)) {'."\n";
        $footer .= str_repeat(" ", 12).'return self::$table[$row]; // return an array'."\n";
        $footer .= str_repeat(" ", 8).'} else {'."\n";
        $footer .= str_repeat(" ", 12).'return array(); // error exit, empty array'."\n";
        $footer .= str_repeat(" ", 8).'}'."\n";
        $footer .= str_repeat(" ", 4).'}'."\n\n";
        $footer .= str_repeat(" ", 4).'public static function getCell($row, $col){'."\n";
        $footer .= str_repeat(" ", 8).'if (array_key_exists($row, self::$table)) {'."\n";
        $footer .= str_repeat(" ", 12).'if (array_key_exists($col,self::$table[$row])) {'."\n";
        $footer .= str_repeat(" ", 16).'return self::$table[$row][$col];'."\n";
        $footer .= str_repeat(" ", 12).'} else {'."\n";
        $footer .= str_repeat(" ", 16).'return null; // error exit 1'."\n";
        $footer .= str_repeat(" ", 12).'}'."\n";
        $footer .= str_repeat(" ", 8).'} else {'."\n";
        $footer .= str_repeat(" ", 12).'return null; // error exit 2'."\n";
        $footer .= str_repeat(" ", 8).'}'."\n";
        $footer .= str_repeat(" ", 4).'}'."\n";
        $footer .= '}'."\n";

        // create body from $this->menutree
        $body = "";
        foreach ($this->menutree as $menuitems){
            $body .= str_repeat(" ", 8)."'".$menuitems["ROUTE"]."' => array(\n";
            foreach ($menuitems as $key => $item){
                if ($key != "ROUTE"){
                    $body .= str_repeat(" ", 12)."'".$key."' => '".$item."', \n";
                }
            }
            $pos = strripos($body, ",");
            $body[$pos] = ")";
            $body[$pos+1] = ",";
        }
        $pos = strripos($body, ",");
        $body[$pos] = " ";

        return $header.$body.$footer;
    }

    private function initmenutree(Session $session)
    {   /**
         * get the data in $menutree from the database table itixi.menutree.
         * create derived files (persistent) for efficient access to the menutree data
         */
        try {
            // make a database call to get the menu items
            $conn = $this->get('database_connection');
            $this->menutree = $conn->fetchAll('SELECT * from itixi.menutree');

            // write grid as new file MenuTree.php to the filesystem
            $fs = new Filesystem();
            try {
                $fs->dumpFile( '..'.PHP_MENUTREE, $this->makeMenutreePhp() );
            } catch (IOException $e) {
                $session->set("errormsg","PHP Menutree IO filesystem error: ".$e);
            }

            // use grid to create a twig file
            $this->initMenuHtmlTwig($session); // proceed to create a twig file

        } catch (PDOException $e) {
            $session->set("errormsg","Cannot access itixi database : ".$e);
        }
    }

    private function initSession(Session $session)
    {   /**
         * initialize all variables for the iTixi application and for all twig files.
         * rebuilt after each build of the iTixi application (when initMenutree=true).
         */

     // initialize all variables in the itixi user session
        $session->set("title", TIXI_UNDEFINED);
        $session->set("baseurl",TIXI_UNDEFINED);
        $session->set("username",TIXI_UNDEFINED);
        $session->set("userroles",array());
        $session->set("permission", TIXI_UNDEFINED);
        $session->set("customer",TIXI_UNDEFINED);
        $session->set("breadcrumb", TIXI_UNDEFINED);
        $session->set("subject",TIXI_UNDEFINED);
        $session->set("action", TIXI_UNDEFINED);
        $session->set("filter",'');
        $session->set("mode",TIXI_UNDEFINED);
        $session->set("errormsg",TIXI_UNDEFINED);
        $session->set("tainted", TIXI_UNDEFINED);
        $session->set("route", TIXI_UNDEFINED);
        /* $session->set("cursor/$route", $value); // cache cursor in structured namespace */
        /* $session->set("myform/$route", $array); // cache metadata in structured namespace */

     // initialize menutree (conditional)
        if ($this->initMenutree) {
            $this->initMenutree = false;
            $this->get('logger')->debug('TIXI initializing menutree');
            $this->initmenutree($session);
        }
    }

    public function setTemplateParameters( $route )
    {   /**
         * dynamically set / reset the session parameters in the iTixi application.
         * this service is called in each and every controller of the iTixi app.
         * input: $route the route defined in the bundles config.yml e.g. 'tixi_home_page'
         */

        $this->get('logger')->debug('TIXI transaction page '.$route);
        $errormsg = ''; // error messages in this function

    // initialize session attributes
        $session = new Session();
        if ($session->has("title")==false)
        {
            $this->get('logger')->debug('TIXI initializing session variables');
            $this->initSession($session);
        };
        $tixi = $this->container->getParameter('tixi'); // array with constants

    // set title parameter in the session (conditional)
        $session->set("title", $tixi["title"]." - ".menutree::getCell($route,"CAPTION"));

    // set common parameters
        $session->set("baseurl",'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);

    // set parameters for the top frame
        $session->set("breadcrumb", menutree::getCell($route,"BREADCRUMB"));

    // check for changed username in this session
        $usr = $this->getUser();
        if (is_object($usr)) {
            $username = $usr->getUsername();
            if ($username != $session->get("username"))
                {
                    $session->set("username", $username);
                    $part1 = explode('@', $username);
                    $part2 = explode('.',$part1[1]);
                    $session->set("customer", $part2[0]);
                    $session->set("userroles", $usr->getRoles());
                };
        } else {
            $session->set( "username", $tixi["undefined"] );
            $session->set( "userroles", array());
            $session->set( "customer", $tixi["undefined"] );
        };
        $session->set("permission", menutree::getCell($route,"PERMISSION"));

    // reset parameters for the subject
        $session->set("subject", $tixi["undefined"] );

    // set parameters for actions
        if (isset($_REQUEST['action'])) {
            $session->set('action', $_REQUEST['action'] );
            if ($_REQUEST['action'] == 'select') {
                $session->set("cursor/$route", $_REQUEST['cursor']);
            }
        } else {
            // no action in request
            $session->set('action', '' ); // action is empty
            $session->set('mode', ''); // mode is empty too
            // @todo: check if an incomplete transaction is pending
            // e.g. mode = mode_edit_in_list or mode_edit_record
            // if yes, redirect back to the prevoius page with an error message
        };
        if ($session->get('action') == 'print'){
            $errormsg .= "Aktion(print) wird auf diese Seite nicht unterstützt.";
        }
        $session->set('route', $route); // set current route

    // set filters
        if (isset($_REQUEST['filter'])) {
            $session->set("filter", $_REQUEST['filter']);
        }

    // reset error message
        $session->set('errormsg', $errormsg);

    return;
    }
}