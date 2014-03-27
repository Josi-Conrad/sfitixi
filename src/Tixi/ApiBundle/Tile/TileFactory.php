<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 23:46
 */

namespace Tixi\ApiBundle\Tile;


use Tixi\ApiBundle\Tile\Core\RootPanel;

class TileFactory {

    public static function createGetFormTile($displayName, $form) {
        $rootPanel = new RootPanel($displayName);

    }

} 