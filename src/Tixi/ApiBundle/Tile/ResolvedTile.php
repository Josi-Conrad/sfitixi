<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 12:45
 */

namespace Tixi\ApiBundle\Tile;


class ResolvedTile {

    public $viewIndentifiers;

    public $rawData;

    public function __construct(array $viewIdentifiers, $rawData) {
        $this->viewIndentifiers = $viewIdentifiers;
        $this->rawData = $rawData;
    }

} 