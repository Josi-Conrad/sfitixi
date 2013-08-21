<?php

namespace Tixi\Pub\AboutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($slug='')
    {
     // set parameters for the rendering of the about page
        $paramservice = $this->get('tixi_homepage_service');

     // render the about page
        return $this->render('TixiPubAboutBundle:Default:index.html.twig',
            $paramservice->getTemplateParameters('about', 'Informationen zu iTixi')
        );
    }
}
