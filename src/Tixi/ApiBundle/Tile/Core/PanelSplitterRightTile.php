<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 01.04.14
 * Time: 15:29
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class PanelSplitterRightTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class PanelSplitterRightTile extends AbstractTile{
    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:passthrough.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'rightpanelsplitter';
    }
}