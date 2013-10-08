<?php

// src/Tixi/App/TeamBundle/Resources/views/Default/index.html.twig
// 28.08.2013 martin jonasse initial file
// 03.09.2013 martin jonasse renamed getTemplateParamenters to setTemplateParameters, added $mode
// 30.09.2013 martin jonasse implemented first version of StateBuilder

namespace Tixi\App\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Tixi\HomeBundle\Controller\StateBuilder;
use Tixi\HomeBundle\Controller\ListBuilder;

class DefaultController extends Controller
{
    public function indexAction($name='')
    {
    // set parameters for the rendering of the team data page
        $tixi_housekeeper = $this->get('tixi_housekeeper');
        $tixi_housekeeper->setTemplateParameters('tixi_unterhalt_teamdaten_page');

    // set subject
        $session = $this->container->get('session');
        $session->set('subject', 'Teamdaten (liste)');

    // set states according to actions
        $state = new StateBuilder($this->container);
        $state->setListObjectStates();

    // build list according to state
        $list = $this->get('tixi_listbuilder'); // service
        $list->setView('vbenutzerperson');
        if ($session->get('mode') == $this->container->getParameter('tixi')["mode_select_list"] ) {
            $list->makeList();
        }

    // get / set password (hash)
    // @todo: get / set hashed password from database

    // render the team data page
        return $this->render('TixiAppTeamBundle:Default:index.html.twig',
                    array('message' => $state->getMessage(),
                        'myheader' => $list->getHeader(),
                        'myrows' => $list->getRows(),
                    ));
    }
}
