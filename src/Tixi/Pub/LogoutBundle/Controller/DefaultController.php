<?php

// src/Tixi/Pub/LogoutBundle/DefaultController.php
// 23.08.2013 martin jonasse initial file
// 28.08.2013 martin jonasse added logout code: security context and session
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters

namespace Tixi\Pub\LogoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        // logout the current user
        $this->get('security.context')->setToken(Null);
        $this->get('request')->getSession()->invalidate();

        // set parameters for the rendering of the about page
        $paramservice = $this->get('tixi_homepage_service');

        // render the about page
        return $this->render('TixiPubLogoutBundle:Default:index.html.twig',
            $paramservice->setTemplateParameters('logout', 'Informationen zur iTixi logout Funktion')
        );
    }
}
