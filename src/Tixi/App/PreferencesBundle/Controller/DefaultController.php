<?php

namespace Tixi\App\PreferencesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        // set parameters for the rendering of the about page
        $paramservice = $this->get('tixi_homepage_service');

        // render the about page
        return $this->render('TixiAppPreferencesBundle:Default:index.html.twig',
            $paramservice->getTemplateParameters('support', 'Informationen zur Support der iTixi Applikation')
        );
    }
}
