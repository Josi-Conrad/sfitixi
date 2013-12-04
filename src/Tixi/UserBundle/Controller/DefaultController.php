<?php

namespace Tixi\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function indexAction()
    {
        throw new AccessDeniedException();

        return $this->redirect($this->generateUrl('tixi_home_page'));
    }
}
