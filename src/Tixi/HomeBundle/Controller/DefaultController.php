<?php

// src/Tixi/HomeBundle/Controller/DefaultController.php
// 12.08.2013 martin jonasse initial file
// 26.08.2013 martin jonasse added errormsg code
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($slug = '')
    {
     // this controller is called for all /home requests

     // test for actions: $act = NULL, add, modify, delete, save, cancel, filter, print
        if (isset($_REQUEST['action'])) {
            $act = $_REQUEST['action'];
            $err = "Aktionen ($act) werden auf diese Seite nicht unterstüzt.";
        }
        else {
            $err = '';
        };

     // set mode (test)
     //   $session = $this->container->get('session');
     //   $mode = $session->get('mode_edit_record');
        $mode = '';

        // render /home/ page
        $paramservice = $this->get('tixi_homepage_service');
        return $this->render(
            'TixiHomeBundle:Default:index.html.twig',
            $paramservice->setTemplateParameters('home', 'Startseite der Dispo-Software', $mode, $err)
        );
    }
}
