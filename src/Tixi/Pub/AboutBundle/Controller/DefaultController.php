<?php

namespace Tixi\Pub\AboutBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($slug='')
    {
        // set common parameters
        $baseurl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; // root url

     // set parameters for the top frame
        $application = 'iTixi';     // @todo: get this from session (conditional)
        $version = '2.0.4';         // @todo: get this from session (conditional)
        $customer = 'Undefined';    // @todo: get this from session (conditional)
        $breadcrumbs = 'Breadcrumbs (@todo)';
        $username = 'Anonym';       // @todo: get this from session (conditional)


        return $this->render('TixiPubAboutBundle:Default:index.html.twig',
               array('application' => $application, 'version' => $version, 'customer' => $customer,
                'breadcrumbs' => $breadcrumbs, 'username' => $username, 'baseurl' => $baseurl )
        );
    }
}
