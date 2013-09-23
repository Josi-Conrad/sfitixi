<?php

// src/Tixi/App/TeamBundle/Resources/views/Default/index.html.twig
// 28.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters, added $mode

namespace Tixi\App\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
    // set parameters for the rendering of the about page
        $tixi_housekeeping = $this->get('tixi_housekeeping');
        $tixi_housekeeping->setTemplateParameters('tixi_unterhalt_teamdaten_page');

    // set subject and mode
        $session = $this->container->get('session');
        $session->set('mode', 'mode_select_list'); // provisional
        $session->set('subject', 'Teamdaten (liste)');

    // get / set password (hash)
    // @todo: get / set hashed password from database

    // render the about page
        return $this->render('TixiAppTeamBundle:Default:index.html.twig');
    }
}
