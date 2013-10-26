<?php

// src/Tixi/App/TeamBundle/Resources/views/Default/index.html.twig
// 28.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters, added $mode
// 30.09.2013 martin jonasse implemented first version of StateBuilder

namespace Tixi\App\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\StateBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;
use Tixi\HomeBundle\Controller\FormBuilder;

class DefaultController extends Controller
{
    public function validateTeamdata($myform=array())
    {/*
      * callback function for validating the formdata for special conditions
      * please note: passing $myform as a reference is not possible
      * return: true = OK, false = Error (errormsg already set in session)
      */
        foreach ($myform as $key => $values) {
            if ($values["Field"] == "passwort") {
                if (strlen($values["Value"]) < 8) {
                    $myform[$key]["Error"] = "Validierungsfehler: Passwörter müssen mindestens 8 Zeichen lang sein.";
                }
            }
        }
        return $myform; // return the changed local copy of the myform array
    }

    public function indexAction($name='')
    {
    // set local variables
        $page = 'tixi_unterhalt_teamdaten_page';
        $pkey = 'benutzer_id';
        $session = $this->container->get('session');
        $tixi = $this->container->getParameter('tixi');

    // set parameters for the rendering of this page
        $tixi_housekeeper = $this->get('tixi_housekeeper');
        $tixi_housekeeper->setTemplateParameters($page);

    // set states according to actions
        $state = $this->get('tixi_formstatebuilder'); // start service
        $state->setFormView('form_benutzer_person');
        $state->setPkey($pkey);
        $state->setCallback(array($this, "validateTeamdata"));
        $state->setListObjectStates($page);

    // rendering options
        if ($session->get('mode') == $tixi["mode_select_list"])
        {/*
          * display a list of team members
          */
            $session->set('subject', 'Teamdaten (liste)');

            $list = $this->get('tixi_listbuilder'); // start service
            $list->setListView('list_benutzer_person');
            $list->setPkey($pkey);
            $list->makeList();
            // render list
            return $this->render('TixiAppTeamBundle:Default:list.html.twig',
                           array('myheader' => $list->getHeader(),
                                 'myrows' => $list->getRows() ));

        } elseif ($session->get('mode') == $tixi["mode_edit_in_list"])
        {/*
          * display a form for the selected team member
          */
            $cursors = $session->get('cursors');
            $session->set('subject', 'Teamdaten['.$cursors[$page].']');
            // render form
            return $this->render('TixiAppTeamBundle:Default:form.html.twig',
                           array('myform' => $state->getFormMetaData() ));

        } else {
            $msg = "Unerwartete Zustand $session->get('mode') in Seite $page.";
            $session->set('errormsg', $msg);
            trigger_error( $msg );
            return $this->render('TixiAppTeamBundle:Default:form.html.twig' );
        }
    }
}
