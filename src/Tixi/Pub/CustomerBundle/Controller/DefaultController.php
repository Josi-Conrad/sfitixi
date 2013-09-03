<?php
// src/Tixi/Pub/CustomerBundle/Controller/DefaultController
// 25.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters

namespace Tixi\Pub\CustomerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        // set parameters for the rendering of the about page
        $paramservice = $this->get('tixi_homepage_service');

        // render the about page
        return $this->render('TixiPubCustomerBundle:Default:index.html.twig',
            $paramservice->setTemplateParameters('customer', 'Informationen zum Mandant')
        );
    }
}
