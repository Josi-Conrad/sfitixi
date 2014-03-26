<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 12:45
 */

namespace Tixi\ApiBundle\Tile;


class ResolvedTile {

    protected $foo = 'foo';
    protected $bar = 'bar';


    function test() {
        return array($this->$foo=>$this->$bar);
    }

} 