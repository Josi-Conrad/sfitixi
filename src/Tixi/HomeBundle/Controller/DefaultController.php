<?php

// src/Tixi/HomeBundle/Controller/DefaultController.php
// 12.08.2013 martin jonasse initial file
// 26.08.2013 martin jonasse added errormsg code

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($slug = '')
    {
     // called for all /home requests

     // test for actions: $act = NULL, add, modify, delete, save, cancel, filter, print
        if (isset($_REQUEST['action'])) {
            $act = $_REQUEST['action'];
            $err = "Aktionen ($act) werden auf diese Seite nicht unterstüzt.";
        }
        else {
            $act = NULL;
            $err = '';
        };

     // render /home/ page
        $paramservice = $this->get('tixi_homepage_service');

        $usr = $this->getUser();
        if (is_object($usr)) { $username = $usr->getUsername(); };

        return $this->render(
            'TixiHomeBundle:Default:index.html.twig',
            $paramservice->getTemplateParameters('home', 'Startseite der Dispo-Software',$err)
        );
    }
}
