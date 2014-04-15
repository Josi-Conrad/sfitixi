<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 13.04.14
 * Time: 15:09
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class MenuTile extends AbstractTile{

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:menu.html.twig';
    }

    public function getName()
    {
        return 'menu';
    }
}