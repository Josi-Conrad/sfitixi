<?php

// src/Tixi/App/PreferencesBundle/Controller/DefaultController
// 28.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters, added $mode

namespace Tixi\App\PreferencesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
    // set parameters for the rendering of the preferences page
        $tixi_housekeeper = $this->get('tixi_housekeeper');
        $tixi_housekeeper->setTemplateParameters('tixi_preferences_page');

    // set subject
        $session = $this->container->get('session');
        $usr = $session->get('username');
        $session->set('subject', 'Einstellungen für Benutzer '.$usr);

    // get / set password (hash)
    // @todo: get / set hashed password from database

    // render the about page
        return $this->render('TixiAppPreferencesBundle:Default:index.html.twig');
    }
}
