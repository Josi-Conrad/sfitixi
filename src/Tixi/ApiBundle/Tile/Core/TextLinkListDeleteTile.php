<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 10.04.14
 * Time: 00:48
 */

namespace Tixi\ApiBundle\Tile\Core;


class TextLinkListDeleteTile extends TextLinkTile{

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:textlinklistdelete.html.twig';
    }

    public function getName()
    {
        return 'textlinklistdelete';
    }

} 