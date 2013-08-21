<?php

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($slug = '')
    {
     // called for all /home requests

     // test for actions
        if (isset($_REQUEST['action'])) { $act = $_REQUEST['action']; }
        else { $act = NULL; }; // $act = NULL, add, modify, delete, save, cancel, filter

     // render /home/ page
        $paramservice = $this->get('tixi_homepage_service');
        return $this->render(
            'TixiHomeBundle:Default:index.html.twig',
            $paramservice->getTemplateParameters('home', 'Startseite der Dispo-Software')
        );
    }
}
