<?php

// src/Tixi/SecurityBundle/Controller/SecurityControlle.php
// 26.08.2013 martin jonasse initial file, credit the book - chapter 13 - security
// 07.10.2013 martin jonasse simplified design, fixed error message

namespace Tixi\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    public function loginAction()
    {/*
      * login to the iTixi application with username (name@company.ch)
      * and password (which will be encrypted with salt)
      */
        // initialize page
        $tixi_housekeeper = $this->get('tixi_housekeeper');
        $tixi_housekeeper->setTemplateParameters('tixi_login');

        // set subject
        $request = $this->getRequest();
        $session = $request->getSession();
        $session->set('subject', 'Anmelden an der iTixi Applikation');

      // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get( SecurityContext::AUTHENTICATION_ERROR );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        if ($error!= Null) {
            $session->set('errormsg', 'Fehler bei der Anmeldung (Benutzername und/oder Passwort falsch)');
        }
        return $this->render( 'TixiSecurityBundle:Security:login.html.twig' );
    }
}
