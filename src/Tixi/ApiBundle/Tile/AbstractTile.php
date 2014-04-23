<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 12:42
 */

namespace Tixi\ApiBundle\Tile;

/**
 * Class AbstractTile
 * @package Tixi\ApiBundle\Tile
 */
abstract class AbstractTile {

    protected $children = array();
    protected $vistor = null;
    protected $parent = null;

    /**
     * @param AbstractTile $child
     * @return AbstractTile
     */
    public function add(AbstractTile $child) {
        $child->setParent($this);
        $this->children[] = $child;
        return $child;
    }

    /**
     * @param AbstractTile $parent
     */
    public function setParent(AbstractTile $parent) {
        $this->parent = $parent;
    }

    /**
     * @return null
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @return array
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * @return int|void
     */
    public function getAmountOfChildren() {
        return count($this->children);
    }

    /**
     * @return null
     */
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

    /**
     * @return array
     */
    public function getViewIdentifiers(){
        return array();
    }

    /**
     * @return array
     */
    public function getViewParameters() {
        return array();
    }

    /**
     * @return mixed
     */
    public abstract function getTemplateName();

    /**
     * @return mixed
     */
    public abstract function getName();

} 