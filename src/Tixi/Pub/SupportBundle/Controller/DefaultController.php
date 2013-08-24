<?php

// src/Tixi/Pub/SupportBundle/Controller/DefaultController.php
// 24.08.2013 martin jonasse initial file

namespace Tixi\Pub\SupportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        // set parameters for the rendering of the about page
        $paramservice = $this->get('tixi_homepage_service');

        // render the about page
        return $this->render('TixiPubSupportBundle:Default:index.html.twig',
            $paramservice->getTemplateParameters('support', 'Informationen zur Support der iTixi Applikation')
        );
    }
}
