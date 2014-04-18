<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.03.14
 * Time: 14:33
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class SelectionButtonDividerTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class SelectionButtonDividerTile extends AbstractTile{
    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:selectionbuttondivider.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'selectionbuttondivider';
    }
}