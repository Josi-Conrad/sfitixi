<?php

// src/Tixi/HomeBundle/Controller/DefaultController.php
// 12.08.2013 martin jonasse initial file
// 26.08.2013 martin jonasse added errormsg code
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction($slug = '')
    {
     // initialize home page
        $tixiservice = $this->get('tixi_homepage_service');
        $tixiservice->setTemplateParameters('tixi_home_page');

     // set subject
        $session = $this->container->get('session');
        $session->set('subject', 'Homepage der iTixi application');

     // test for actions: $act = NULL, add, modify, delete, save, cancel, filter, print
        if (isset($_REQUEST['action'])) {
            $act = $_REQUEST['action'];
            $session->set('errormsg', "Aktion($act) wird auf diese Seite nicht unterstüzt." );
        }

        // render /home/ page
        return $this->render( 'TixiHomeBundle:Default:index.html.twig' );
    }
}
