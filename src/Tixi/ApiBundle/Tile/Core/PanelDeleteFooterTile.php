<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.04.14
 * Time: 17:16
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class PanelDeleteFooterTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class PanelDeleteFooterTile extends AbstractTile{
    /**
     * @param $targetSrc
     * @param $deleteText
     */
    public function __construct($targetSrc, $deleteText) {
        $this->add(new DeleteButtonTile('delete', $targetSrc, $deleteText));
    }

    /**
     * @return array
     */
    public function getViewIdentifiers() {
        return array('panelfooter');
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:paneldeletefooter.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'deletefooter';
    }
}