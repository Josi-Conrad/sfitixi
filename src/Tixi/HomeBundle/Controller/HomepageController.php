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

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

define("TIXI_UNDEFINED", "undefined");
define("TIXI_MENUTREE", "/src/Tixi/HomeBundle/Resources/views/Default/menu.html.twig");

Class HomepageController extends Controller
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
        $myfile = "{# ".TIXI_MENUTREE." #}\n";
        $myfile .= "{# built automatically by the iTixi application #}\n";
        $myfile .= $this->makeMenuHtmlTwig($session);

        // write new file to the filesystem as menu.html.twig
        $fs = new Filesystem();
        try {
            $fs->dumpFile( '..'.TIXI_MENUTREE, $myfile );
        } catch (IOException $e) {
            $session->set("errormsg","IO filesystem error: ".$e);
        }
    }

    private function initmenutree(Session $session)
    {   /**
         * get the data in $menutreefrom the database table itixi.menutree.
         * and also store some menuitems to the session (persistent)
         */
        try {
            // make a database call to get the menu items
            $conn = $this->get('database_connection');
            $this->menutree = $conn->fetchAll('SELECT * from itixi.menutree');

            // add all captions to the session (persistant)
            $myarray = array();
            foreach ($this->menutree as $menuitems) {
                $myarray[$menuitems["ROUTE"]] = $menuitems["CAPTION"];
            }
            $session->set( 'captions', $myarray );

            // add all permissions to the session (persistant)
            reset( $this->menutree );
            $myarray = array();
            foreach ($this->menutree as $menuitems) {
                $myarray[$menuitems["ROUTE"]] = $menuitems["PERMISSION"];
            }
            $session->set( 'permissions', $myarray );

            // create a twig file
            $this->initMenuHtmlTwig($session); // proceed to create a twig file

        } catch (PDOException $e) {
            $session->set("errormsg","Cannot access itixi database : ".$e);
        }
    }

    private function initSession(Session $session)
    {   /**
         * initialize all constants and variables available to the iTixi application and in all twig files.
         * rebuilt after each build of the iTixi application (when initMenutree=true).
         */

     // initialize all constants available in the iTixi application
        $session->set("const_application", "iTixi");
        $session->set("const_version", "2.0.0");
        $session->set("const_filter", "Filter...");
        $session->set("const_title", "iTixi cloud computing");
        $session->set("const_undefined", "undefined");
        $session->set("mode_read_record", "read record");
        $session->set("mode_edit_record", "edit record");
        $session->set("mode_select_list", "select list");
        $session->set("mode_edit_in_list", "edit in list");

     // initialize all attributes available in the itixi user session
        $session->set("title", TIXI_UNDEFINED);
        $session->set("baseurl",TIXI_UNDEFINED);
        $session->set("username",TIXI_UNDEFINED);
        $session->set("userroles",TIXI_UNDEFINED);
        $session->set("customer",TIXI_UNDEFINED);
        $session->set("breadcrumbs",TIXI_UNDEFINED);
        $session->set("subject",TIXI_UNDEFINED);
        $session->set("action", TIXI_UNDEFINED);
        $session->set("cursor", TIXI_UNDEFINED);
        $session->set("filter",TIXI_UNDEFINED);
        $session->set("mode",TIXI_UNDEFINED);
        $session->set("errormsg",TIXI_UNDEFINED);

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
        if ($session->has("const_application")==false)
        {
            $this->get('logger')->debug('TIXI initializing session variables');
            $this->initSession($session);
        };

    // set title parameter in the session (conditional)
        $caps = $session->get("captions");
        $session->set("title", ($session->get("const_title")." - ".$caps[ $route ]));

    // set common parameters
        $session->set("baseurl",'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);

    // set parameters for the top frame
        $session->set("breadcrumbs", '...'); // @todo: implement this function point

    // check for changed username in this session
        $usr = $this->getUser();
        if (is_object($usr)) {
            $username = $usr->getUsername();
            if ($username != $session->get("username"))
                {
                    $session->set("username", $username);
                    $parts = explode('@', $username);
                    $session->set("customer", $parts[1]);
                    $session->set("roles", $usr->getRoles());
                };
        } else {
            $session->set("username",TIXI_UNDEFINED);
            $session->set("userroles",TIXI_UNDEFINED);
            $session->set("customer",TIXI_UNDEFINED);
        };

    // reset parameters for the subject
        $session->set("subject", TIXI_UNDEFINED);

    // set parameters for actions
        if (isset($_REQUEST['action'])) {
            $session->set('action', $_REQUEST['action'] );
            if ($_REQUEST['action'] == 'select') {
                 $cursor = (isset($_REQUEST['cursor']) ? $_REQUEST['cursor'] : 1);
            }
        } else {
            $session->set('action', '' );
        };
        if ($session->get('action') == 'print'){
            $errormsg .= "Aktion(print) wird auf diese Seite nicht unterstützt.";
        }

    // set / reset filters
        if (isset($_REQUEST['filter'])) {
            $session->set("filter", $_REQUEST['filter']);
        } else {
            $session->set("filter", $session->get("const_filter"));
        };

    // reset error message
        $session->set('errormsg', $errormsg);

    return;
    }
}