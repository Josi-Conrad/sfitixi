<?php

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($slug = '')
    {
     // set common parameters
        $baseurl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; // root url

     // set parameters for the top frame
        $application = 'iTixi';     // @todo: get this from session (conditional)
        $version = '2.0.4';         // @todo: get this from session (conditional)
        $customer = 'Undefined';    // @todo: get this from session (conditional)
        $breadcrumbs = 'Breadcrumbs (@todo)';
        $username = 'Anonym';       // @todo: get this from session (conditional)

     // set parameters for the menubar

     // set parameters for the subject
        $subject = 'Startseite für Benutzer Anonym';

     // set parameters for the taskbar
        if (isset($_REQUEST['filter'])) { $filter = $_REQUEST['filter']; }
        else {$filter = "Filter..."; };
        $mode = 'read'; // mode = edit, read, NULL @todo: get this from session (conditional)
        $request = $this->container->get('request');
        $routeid = $request->get('_route'); // the current url from the request

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
