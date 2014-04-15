<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 31.03.14
 * Time: 20:09
 */

namespace Tixi\ApiBundle\Tile\Core;


class TextLinkSelectionTile extends TextLinkTile{

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:textlinkselection.html.twig';
    }

    public function getName()
    {
        return 'textlinkselection';
    }
} 