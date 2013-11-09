<?php

namespace Tixi\App\VehicleServiceplanBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        return $this->render('TixiAppVehicleServiceplanBundle:Default:index.html.twig', array('name' => $name));
    }
}
