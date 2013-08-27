<?php

// src/Tixi/Pub/LogoutBundle/DefaultController.php
// 23.08.2013 martin jonasse inital file
// 27.08.2013 @todo: reset the user somehow

namespace Tixi\Pub\LogoutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        // set parameters for the rendering of the about page
        $paramservice = $this->get('tixi_homepage_service');

        // render the about page
        return $this->render('TixiPubLogoutBundle:Default:index.html.twig',
            $paramservice->getTemplateParameters('logout', 'Informationen zur iTixi logout Funktion')
        );
    }
}
