<?php

// src/Tixi/Pub/LogoutBundle/DefaultController.php
// 23.08.2013 martin jonasse initial file
// 28.08.2013 martin jonasse added logout code: security context and session
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters

namespace Tixi\Pub\LogoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
    // logout the current user
        $this->get('security.context')->setToken(Null);
        $this->get('request')->getSession()->invalidate();

    // set parameters for the rendering of the logout page
        $tixi_housekeeping = $this->get('tixi_housekeeping');
        $tixi_housekeeping->setTemplateParameters('tixi_logout_page');

    // set subject
        $session = $this->container->get('session');
        $session->set('subject', 'Informationen zur iTixi logout Funktion');

    // render the logout page
        return $this->render('TixiPubLogoutBundle:Default:index.html.twig');
    }
}
