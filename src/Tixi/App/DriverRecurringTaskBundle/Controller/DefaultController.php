<?php

namespace Tixi\App\DriverRecurringTaskBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // set parameters for the rendering of the support page
        $tixi_housekeeper = $this->get('tixi_housekeeper');
        $tixi_housekeeper->setTemplateParameters('tixi_fahrer_dauereinsatz_page');

        // set subject
        $session = $this->container->get('session');
        $session->set('subject', 'Fahrer Dauereinsatz Plan');

        // render the support page
        return $this->render('TixiAppDriverRecurringTaskBundle:Default:index.html.twig');
    }
}
