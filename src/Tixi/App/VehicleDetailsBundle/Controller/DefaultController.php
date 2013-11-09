<?php

namespace Tixi\App\VehicleDetailsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
        return $this->render('TixiAppVehicleDetailsBundle:Default:index.html.twig', array('name' => $name));
    }
}
