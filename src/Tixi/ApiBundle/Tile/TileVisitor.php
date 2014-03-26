<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 14:12
 */

namespace Tixi\ApiBundle\Tile;


class TileVisitor {

    protected $vists = 0;
    protected $expectedVisits;
    protected $tile;

    public function __construct(AbstractTile $tile) {
        $this->tile = $tile;
        $this->expectedVisits = count($tile->getChildren());
    }

    public function getNextChildToVisit() {
        if($this->vists>=$this->expectedVisits) {
            return -1;
        }else {
            return ++$this->visits;
        }
    }
} 