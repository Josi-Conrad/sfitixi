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

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

define("TIXI_UNDEFINED", "undefined");

Class HomepageController extends Controller
{
    protected $container;

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function initSession(Session $session)
    {
     // initialize all constants available in the iTixi application
     // global to the whole application, not hampered by namespace
        $session->set("const_application", "iTixi");
        $session->set("const_version", "2.0.0");
        $session->set("const_filter", "Filter...");
        $session->set("const_title", "iTixi cloud computing");
        $session->set("const_undefined", "undefined");
        $session->set("mode_read_record", "read record");
        $session->set("mode_edit_record", "edit record");
        $session->set("mode_select_list", "select list");
        $session->set("mode_edit_list_element", "edit list");

     // initialize all attributes available in the itixi user session
        $session->set("title", TIXI_UNDEFINED);
        $session->set("baseurl",TIXI_UNDEFINED);
        $session->set("username",TIXI_UNDEFINED);
        $session->set("userroles",TIXI_UNDEFINED);
        $session->set("customer",TIXI_UNDEFINED);
        $session->set("breadcrumbs",TIXI_UNDEFINED);
        $session->set("subject",TIXI_UNDEFINED);
        $session->set("filter",TIXI_UNDEFINED);
        $session->set("mode",TIXI_UNDEFINED);
        $session->set("errormsg",TIXI_UNDEFINED);
    }

    public function setTemplateParameters( $title, $subject, $mode='', $errormsg='')
    {
        $this->get('logger')->debug('TIXI transaction page '.$title);

    // initialize session attributes
        $session = new Session();
        if ($session->has("const_application")==false)
        {
            $this->get('logger')->debug('TIXI initializing session variables');
            $this->initSession($session);
        };
        $session->set("title", $session->get("const_title")." - ".$title);
        $session->set("subject", $subject);
        $session->set("mode", $mode);
        $session->set("errormsg", $errormsg);

    // set common parameters
        $session->set("baseurl",'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']);

    // set parameters for the top frame
        $session->set("breadcrumbs", '...'); // @todo: implement this function point

    // get username, roles and customer from session
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

    // set parameters for the menubar
    //  baseurl, see above

    // set parameters for the subject
    //  subject, see above

    // set parameters for the taskbar
        if (isset($_REQUEST['filter']))
        {
            $session->set("filter", $_REQUEST['filter']);
        } else {
            $session->set("filter", $session->get("const_filter"));
        };

    // set parameters for the error message
    // $errormsg = '', no error message)

    return array ('dummy' => 'dummy');
    }
}