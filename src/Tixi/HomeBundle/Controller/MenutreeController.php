<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jonasse
 * Date: 13.08.13
 * Time: 09:11
 * 26.08.2013 martin jonasse added action item for database connection
 */
// src/Tixi/HomeBundle/Controller/MenutreeController.php

namespace Tixi\HomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenutreeController extends Controller
{
    public function menuitemsAction()
    {
        // make a database call to get the menu items
        $conn = $this->get('database_connection'); // @todo: fix this quick and dirty item

        // connected to database defined in parameters.yml (itixi)
        $menuitems = $conn->fetchAll('SELECT * from menutree');

        return $this->render(
            'TixiHomeBundle:Default:menu.html.php',
            array('menuitems' => $menuitems)
        );
    }
}