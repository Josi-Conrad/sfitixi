<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 10.04.14
 * Time: 00:48
 */

namespace Tixi\ApiBundle\Tile\Core;


class TextLinkSelectionDeleteTile extends TextLinkTile{

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:textlinkselectiondelete.html.twig';
    }

    public function getName()
    {
        return 'textlinkselectiondelete';
    }

} 