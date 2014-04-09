<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.04.14
 * Time: 17:16
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class PanelDeleteFooterTile extends AbstractTile{

    public function __construct($targetSrc, $deleteText) {
        $this->add(new DeleteButtonTile($targetSrc, $deleteText));
    }

    public function getViewIdentifiers() {
        return array('panelfooter');
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:paneldeletefooter.html.twig';
    }

    public function getName()
    {
        return 'deletefooter';
    }
}