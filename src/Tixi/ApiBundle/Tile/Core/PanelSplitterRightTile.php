<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 01.04.14
 * Time: 15:29
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class PanelSplitterRightTile extends AbstractTile{

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:passthrough.html.twig';
    }

    public function getName()
    {
        return 'rightpanelsplitter';
    }
}