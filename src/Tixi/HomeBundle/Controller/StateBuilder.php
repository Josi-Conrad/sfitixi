<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 30.09.13
 * Time: 12:15
 */

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class StateBuilder extends Controller
{
    protected $message;
    protected $container;   // container

    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setListObjectStates()
    { /*
       * state machine for one list object
       * inputs: session > mode and session > action
       * outputs: session > mode and session > action and return > $message
       * todo: object management create delete and modify
       */

    // initialize variables
        $session = new session;
        $tixi = $this->container->getParameter('tixi');

    // state machine for a list object
        $action = ($session->get('action'));
        $this->message = $tixi['undefined'];
        if ($action=='') { $session->set('mode', $tixi['mode_select_list']); };
        if ($session->get('mode') == $tixi['mode_select_list']) {
            if ($action == '') {
                // action code for the first call
                $this->message = 'start of the workflow';
            } elseif ($action == 'add') {
                // action code for a new list object
                $this->message = 'actions for adding a new object to the list';
                $session->set('mode', $tixi['mode_edit_in_list']);
            } elseif ($action == 'modify') {
                // action code for modify list object
                $this->message = 'actions for modifying an object in the list';
                $session->set('mode', $tixi['mode_edit_in_list']);
            } else { // state mode_select_list remains the same
                if ($action == 'delete') {
                    // action code for delete list object
                    $this->message = 'actions for deleting an object in the list';
                } elseif ($action == 'select') {
                    // action code for select (changed cursor)
                    $this->message = 'actions for selecting a new object in the list';
                } elseif ($action == 'filter') {
                    // action code for filter (changed filter criteria)
                    $this->message = 'actions for filtering the list with new criteria';
                } elseif ($action == 'print') {
                    // action code for printing
                    $this->message = 'actions for printing the list';
                } else {
                    $session->set('errormsg',
                        'Fehler(1): illegaler action '.$session->get('action').' in state '.$session->get('mode'));
                }
                // action code for displaying list
                $this->message .= ' +';
            }
        } elseif ($session->get('mode') == $tixi['mode_edit_in_list']) {
            if ($action == 'save') {
                // action code for saving the form data to the database
                $this->message = 'actions for saving the list-object to the database';
                $session->set('mode', $tixi['mode_select_list']);
            } elseif ($action == 'cancel') {
                // action code for printing
                $this->message = 'actions for canceling the edited object';
                $session->set('mode', $tixi['mode_select_list']);
            } else {
                $session->set('errormsg',
                    'Fehler(2): illegaler action '.$session->get('action').' in state '.$session->get('mode'));
            }
        } else {
            $session->set('errormsg',
                'Fehler(3): illegaler Zustand '.$session->get('mode'));
        }
    }

    public function getMessage()
    {
        return $this->message;
    }
}