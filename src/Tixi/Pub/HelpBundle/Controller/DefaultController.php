<?php
// src/Tixi/Pub/HelpBundle/Controller/DefaultController.php
// 23.08.2013 martin jonasse initial file

namespace Tixi\Pub\HelpBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($slug='')
    {
//      return $this->render('TixiPubHelpBundle:Default:index.html.twig');
        // set parameters for the rendering of the about page
        $paramservice = $this->get('tixi_homepage_service');

        // render the about page
        return $this->render('TixiPubHelpBundle:Default:index.html.twig',
            $paramservice->getTemplateParameters('help', 'Hilfe zur iTixi Applikation')
        );

    }
}
