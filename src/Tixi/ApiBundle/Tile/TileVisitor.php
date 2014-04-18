<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 14:12
 */

namespace Tixi\ApiBundle\Tile;

/**
 * Class TileVisitor
 * @package Tixi\ApiBundle\Tile
 */
class TileVisitor {

    protected $visits = 0;
    protected $expectedVisits;
    protected $tile;

    /**
     * @param AbstractTile $tile
     */
    public function __construct(AbstractTile $tile) {
        $this->tile = $tile;
        $this->expectedVisits = count($tile->getChildren());
    }

    /**
     * @return int
     */
    public function getNextChildToVisit() {
        if($this->visits>=$this->expectedVisits) {
            return -1;
        }else {
            return $this->visits++;
        }
    }
} 