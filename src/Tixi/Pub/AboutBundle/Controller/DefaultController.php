<?php

// 21.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters

namespace Tixi\Pub\AboutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction($slug='')
    {
     // set parameters for the rendering of the about page
        $paramservice = $this->get('tixi_homepage_service');
        $paramservice->setTemplateParameters('tixi_about_page');

     // set subject
        $session = $this->container->get('session');
        $session->set('subject', 'Informationen zur iTixi Applikation');

     // render the about page
        return $this->render('TixiPubAboutBundle:Default:index.html.twig');
    }
}
