<?php

// src/Tixi/App/TeamBundle/Resources/views/Default/index.html.twig
// 28.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters, added $mode

namespace Tixi\App\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
    // set parameters for the rendering of the about page
        $paramservice = $this->get('tixi_homepage_service');

    // set mode (test)
        $session = $this->container->get('session');
        $mode = $session->get('mode_select_list');

    // render the about page
        return $this->render('TixiAppTeamBundle:Default:index.html.twig',
            $paramservice->setTemplateParameters( 'team', 'Informationen zur Teamdaten ', $mode)
        );
    }
}
