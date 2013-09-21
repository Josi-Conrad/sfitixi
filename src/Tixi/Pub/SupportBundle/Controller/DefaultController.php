<?php

// src/Tixi/Pub/SupportBundle/Controller/DefaultController.php
// 24.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters

namespace Tixi\Pub\SupportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        // set parameters for the rendering of the support page
        $paramservice = $this->get('tixi_homepage_service');
        $paramservice->setTemplateParameters('tixi_support_page');

        // set subject
        $session = $this->container->get('session');
        $session->set('subject', 'Informationen zur Support der iTixi Applikation');

        // render the support page
        return $this->render('TixiPubSupportBundle:Default:index.html.twig');
    }
}
