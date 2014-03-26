<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 12:42
 */

namespace Tixi\ApiBundle\Tile;


abstract class AbstractTile {

    protected $children = array();
    protected $vistor = null;
    protected $parent = null;

    public function add(AbstractTile $child) {
        $child->setParent($this);
        $this->children[] = $child;
    }

    public function setParent(AbstractTile $parent) {
        $this->parent = $parent;
    }

    public function getParent() {
        return $this->parent;
    }

    public function getChildren() {
        return $this->children;
    }

    public function getAmountOfChildren() {
        return count($this->children);
    }

    public function getNextChildToVisit() {
        $child = null;
        if(null ===  $this->vistor) {
            $this->vistor = new TileVisitor($this);
        }
        $nextChildPosition = $this->vistor->getNextChildToVisit();
        if($nextChildPosition!==-1) {
            $child = $this->children[$nextChildPosition];
        }
        return $child;
    }

    public function getViewIdentifiers(){
        return array();
    }

    public function getViewParameters() {
        return array();
    }

    public abstract function getTemplateName();

    public abstract function getName();


} 