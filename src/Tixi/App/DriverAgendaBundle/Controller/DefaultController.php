<?php

namespace Tixi\App\DriverAgendaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // set parameters for the rendering of the support page
        $tixi_housekeeper = $this->get('tixi_housekeeper');
        $tixi_housekeeper->setTemplateParameters('tixi_fahrer_agenda_page');

        // set subject
        $session = $this->container->get('session');
        $session->set('subject', 'Tixi Fahrer Agenda');

        // render the support page
        return $this->render('TixiAppDriverAgendaBundle:Default:index.html.twig');
    }
}
