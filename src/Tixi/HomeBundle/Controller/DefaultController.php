<?php

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($slug = '')
    {
     // parameters for viewing
        $application = 'iTixi';     // @todo: get this from session (conditional)
        $version = '2.0.4';         // @todo: get this from session (conditional)
        $customer = 'undefined';    // @todo: get this from session (conditional)
        $breadcrumbs = 'Startseite';
        $username = 'Anonym';       // @todo: get this from session (conditional)
        $subject = 'Startseite für Benutzer Anonym';

     // parameters for links
        $request = $this->container->get('request');
        $routeid = $request->get('_route');

     // render /home/ page
        return $this->render(
            'TixiHomeBundle:Default:index.html.twig',
            array('application' => $application, 'version' => $version, 'customer' => $customer,
                'breadcrumbs' => $breadcrumbs, 'username' => $username, 'subject' => $subject,
                'routeid' => $routeid)
        );
    }
}
