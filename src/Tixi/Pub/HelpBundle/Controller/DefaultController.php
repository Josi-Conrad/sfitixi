<?php
// src/Tixi/Pub/HelpBundle/Controller/DefaultController.php
// 23.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters

namespace Tixi\Pub\HelpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction($slug='')
    {
//      return $this->render('TixiPubHelpBundle:Default:index.html.twig');
    // set parameters for the rendering of the help page
        $tixi_housekeeping = $this->get( 'tixi_housekeeping' );
        $tixi_housekeeping->setTemplateParameters( 'tixi_help_page' );

    // set subject
        $session = $this->container->get('session');
        $session->set('subject', 'Hilfe zur iTixi Applikation');

    // render the help page
        return $this->render( 'TixiPubHelpBundle:Default:index.html.twig' );

    }
}
