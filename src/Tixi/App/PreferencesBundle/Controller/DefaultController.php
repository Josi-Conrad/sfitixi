<?php

// src/Tixi/App/PreferencesBundle/Controller/DefaultController
// 28.08.2013 martin jonasse initial file

namespace Tixi\App\PreferencesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        // get username from context
        $username = $this->getUser()->getUsername();

        // set parameters for the rendering of the about page
        $paramservice = $this->get('tixi_homepage_service');

        // render the about page
        return $this->render('TixiAppPreferencesBundle:Default:index.html.twig',
            $paramservice->getTemplateParameters('preferences',
                'Einstellungen für Benutzer '.$username)
        );
    }
}
