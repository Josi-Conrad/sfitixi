<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 12:45
 */

namespace Tixi\ApiBundle\Tile;

/**
 * Class ResolvedTile
 * @package Tixi\ApiBundle\Tile
 */
class ResolvedTile {

    public $viewIndentifiers;

    public $rawData;

    /**
     * @param array $viewIdentifiers
     * @param $rawData
     */
    public function __construct(array $viewIdentifiers, $rawData) {
        $this->viewIndentifiers = $viewIdentifiers;
        $this->rawData = $rawData;
    }

} 