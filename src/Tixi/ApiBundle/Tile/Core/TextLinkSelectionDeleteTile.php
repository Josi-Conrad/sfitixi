<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 10.04.14
 * Time: 00:48
 */

namespace Tixi\ApiBundle\Tile\Core;

/**
 * Class TextLinkSelectionDeleteTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class TextLinkSelectionDeleteTile extends TextLinkTile{
    /**
     * @return string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:textlinkselectiondelete.html.twig';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'textlinkselectiondelete';
    }

} 