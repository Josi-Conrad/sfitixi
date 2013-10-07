<?php

// src/Tixi/HomeBundle/Controller/DefaultController.php
// 12.08.2013 martin jonasse initial file
// 26.08.2013 martin jonasse added errormsg code
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters
// 21.09.2013 martin jonasse simplified setTemplateParameters, added $route, dropped all others

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction($slug = '')
    {
     // initialize page
        $tixi_housekeeping = $this->get('tixi_housekeeping');
        $tixi_housekeeping->setTemplateParameters('tixi_home_page');

     // set subject
        $session = $this->container->get('session');
        $session->set('subject', 'Homepage der iTixi Applikation');

     // render /home/ page
        return $this->render( 'TixiHomeBundle:Default:index.html.twig' );
    }
}
