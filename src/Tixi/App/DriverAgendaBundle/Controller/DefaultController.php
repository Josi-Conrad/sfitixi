<?php

namespace Tixi\App\DriverAgendaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\Housekeeper;
use Tixi\HomeBundle\Controller\DataMiner;
use Tixi\HomeBundle\Controller\MenuTree;

class DefaultController extends Controller
{
    protected $session;
    protected $state;     // state of this bundle
    protected $parent_id; // id in the fahrer table
    protected $mydates;   // array which holds the query data

    const tixi_agenda_preview3 = 3;
    const tixi_agenda_preview6 = 6;
    const tixi_agenda_action_preview3 = 'preview1q'; // see Default\index.html.twig
    const tixi_agenda_action_preview6 = 'preview2q'; // see Default\index.html.twig
    const tixi_agenda_action_sendmail = 'sendpreview'; // see Default\index.html.twig

    private function getAgendaData()
    {/*
      * get a few months worth of data from the database, depending on $this->state
      * output: $this->mydates
      */
        $this->mydates = array(0 => array( "a", "b", "c", "d", "e", "f", "g"));
    }

    private function doActions($action)
    {/*
      * perform the actions associated with $myaction
      */
        switch ($action){
            case "":
                /* no action definied, default: display 3 Months */
                $this->getAgendaData();
                break;
            case self::tixi_agenda_action_preview3:
                $this->state = self::tixi_agenda_preview3;
                $this->session->set('agendastate', $this->state);
                $this->getAgendaData();
                break;
            case self::tixi_agenda_action_preview6:
                $this->state = self::tixi_agenda_preview6;
                $this->session->set('agendastate', $this->state);
                $this->getAgendaData();
                break;
            case self::tixi_agenda_action_sendmail:
                $this->getAgendaData();
                $this->session->set('errormsg', "Action ($action), noch nicht implementiert.");
                break;
            default:
                $this->session->set('errormsg', "Unbekannte action ($action), ignoriert.");
        }
    }

    private function setSubject($route)
    {/*
      * set the subject as FAHRER: xyz - AGENDA: uvw
      */
        $subject = strtoupper(MenuTree::getCell($route, "CAPTION"));
        if ($this->state == self::tixi_agenda_preview3) {
            $subject .= ': Vorschau 3 Monate';
        } else {
            $subject .= ': Vorschau 6 Monate';
        }
        $parent = menutree::getCell($route, "PARENT");
        if ($parent != "") {
            $subject = $this->session->get("context/$parent")." - ".$subject;
        }
        $this->session->set('subject', $subject);
    }

    public function indexAction()
    {
        // set parameters for the rendering of the driver agenda page
        $route = 'tixi_fahrer_agenda_page';
        $housekeeper = $this->get('tixi_housekeeper');
        if ($housekeeper->setTemplateParameters($route) != 0)
        {/* user has no permission to use this page */
            return $this->render('TixiHomeBundle:Default:error403.html.twig');
        }

        // get parent context
        $parent = menutree::getCell($route, "PARENT");
        $this->session = $this->container->get('session');
        $this->parent_id = $this->session->get("cursor/$parent");
        if ($this->parent_id == null)
        {/* no parent active in this session, redirect to parent page */
            return $this->redirect($this->generateUrl('tixi_fahrer_page'));
        }

        // get driver agenda state
        if ($this->session->has('agendastate')){
            $this->state = $this->session->get('agendastate');
        } else {/* first time visited in this session */
            $this->state = self::tixi_agenda_action_preview3;
            $this->session->set('agendastate', $this->state);
        }

        // get driver agenda action
        $action = $this->session->get('action');
        $this->doActions($action);

        // set driver agenda subject
        $this->setSubject($route);

        // render the driver agenda page
        return $this->render('TixiAppDriverAgendaBundle:Default:index.html.twig',
                             array( 'mydates' => $this->mydates ));
    }
}
