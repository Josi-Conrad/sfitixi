<?php

namespace Tixi\App\VehicleDataBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        return $this->render('TixiAppVehicleDataBundle:Default:index.html.twig', array('name' => $name));
    }
}
