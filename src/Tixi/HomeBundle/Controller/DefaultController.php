<?php

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($slug = '')
    {
     // set filter using request parameters (carry over in session)
        if (isset($_REQUEST['filter'])) { $filter = $_REQUEST['filter']; }
        else {$filter = "Filter..."; };

     // parameters for viewing (constants)
        $application = 'iTixi';     // @todo: get this from session (conditional)
        $version = '2.0.4';         // @todo: get this from session (conditional)
        $customer = 'Undefined';    // @todo: get this from session (conditional)
        $breadcrumbs = 'Breadcrumbs (@todo)';
        $username = 'Anonym';       // @todo: get this from session (conditional)
        $subject = 'Startseite für Benutzer Anonym';
        $baseurl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];

     // mode = 'edit' ¦ 'read'
        $mode = 'read';             // @todo: get this from session (conditional)

     // parameters for links
        $request = $this->container->get('request');
        $routeid = $request->get('_route');

     // render /home/ page
        return $this->render(
            'TixiHomeBundle:Default:index.html.twig',
            array('application' => $application, 'version' => $version, 'customer' => $customer,
                  'breadcrumbs' => $breadcrumbs, 'username' => $username, 'subject' => $subject,
                  'filter' => $filter, 'baseurl' => $baseurl, 'mode' => $mode,
                  'routeid' => $routeid)
        );
    }
}
