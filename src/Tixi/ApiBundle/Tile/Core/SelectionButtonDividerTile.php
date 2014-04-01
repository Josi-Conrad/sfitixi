<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.03.14
 * Time: 14:33
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class SelectionButtonDividerTile extends AbstractTile{

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:selectionbuttondivider.html.twig';
    }

    public function getName()
    {
        return 'selectionbuttondivider';
    }
}