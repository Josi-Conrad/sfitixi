<?php
// src/Tixi/Pub/CustomerBundle/Controller/DefaultController
// 25.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters

namespace Tixi\Pub\CustomerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        // set parameters for the rendering of the customer page
        $tixi_housekeeping = $this->get('tixi_housekeeping');
        $tixi_housekeeping->setTemplateParameters('tixi_customer_page');

        // set subject
        $session = $this->container->get('session');
        $session->set('subject', 'Informationen zum Mandant');

        // render the about page
        return $this->render('TixiPubCustomerBundle:Default:index.html.twig');
    }
}
