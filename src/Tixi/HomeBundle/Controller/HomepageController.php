<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 21.08.13
 * Time: 16:09
 */
// src/Tixi/HomeBundle/Controller/HomepageController.php

namespace Tixi\HomeBundle\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// Class HomepageController extends Controller
// not needed at the moment: simple and fast

Class HomepageController
{
    public function showAction($_route) {
        var_dump($_route);
    }

    public function getTemplateParameters( $title, $subject, $errormsg='', $content='')
    {
    // set the parameters array for the default home page environment, some are constants, some variables ...
    // call this every time you render the home template, this is implemented as a symfony2 service

    // set common parameters
        $title = 'iTixi cloud computing - '.$title;
        $baseurl = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME']; // root url

    // set parameters for the top frame
        $application = 'iTixi'; // @todo: get this from the session
        $version = '2.0.4'; // @todo: get this from the session
        $customer = 'tixizug'; // @todo: get this from the session
        $breadcrumbs = 'Startseite'; // @todo: solve this function point
        $username = 'Martin Jonasse'; // @todo: get this from the session

    // set parameters for the menubar
    //  $baseurl

    // set parameters for the subject
    //  $subject

    // set parameters for the taskbar
        if (isset($_REQUEST['filter'])) { $filter = $_REQUEST['filter']; } else {$filter = "Filter..."; };
        $mode = 'read'; // mode = edit, read, NULL @todo: get this from the session

    // set parameters for the error message ($errormsg = NULL, no error message)

    // set parameters for the content ($content = NULL, no content)

    return array(
        'title' => $title, 'baseurl' => $baseurl,
        'application' => $application, 'version' => $version, 'customer' => $customer,
        'breadcrumbs' => $breadcrumbs, 'username' => $username, 'subject' => $subject,
        'filter' => $filter, 'mode' => $mode,
        'errormsg' => $errormsg, 'content' => $content);
    }
}