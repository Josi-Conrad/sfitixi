<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 13.04.14
 * Time: 15:09
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class MenuTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class MenuTile extends AbstractTile{
    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:menu.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'menu';
    }
}