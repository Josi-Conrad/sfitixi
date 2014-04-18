<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 01.04.14
 * Time: 15:27
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class PanelSplitterTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class PanelSplitterTile extends AbstractTile{

    protected static $columnSystem = 12;

    protected $leftTile;
    protected $rightTile;

    protected $leftBootstrapRatio;
    protected $rightBootstrapRatio;

    /**
     * @param AbstractTile $toAdd
     * @return AbstractTile
     */
    public function addLeft(AbstractTile $toAdd) {
        return $this->leftTile->add($toAdd);
    }

    /**
     * @param AbstractTile $toAdd
     * @return AbstractTile
     */
    public function addRight(AbstractTile $toAdd) {
        return $this->rightTile->add($toAdd);
    }

    /**
     * @param string $splitRatio
     */
    public function __construct($splitRatio='1:1') {
        $this->leftTile = $this->add(new PanelSplitterLeftTile());
        $this->rightTile = $this->add(new PanelSplitterRightTile());
        $this->setBootstrapRatio($splitRatio);
    }

    /**
     * @return array
     */
    public function getViewParameters() {
        return array('leftRatio'=>$this->leftBootstrapRatio, 'rightRatio'=>$this->rightBootstrapRatio);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:panelsplitter.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'panelsplitter';
    }

    /**
     * @param $stringRatio
     * @throws \Exception
     */
    protected function setBootstrapRatio($stringRatio) {
        $explodedRatio = explode(':', $stringRatio);
        try {
            $leftRatio = intval($explodedRatio[0]);
            $rightRation = intval($explodedRatio[1]);
        }catch(\Exception $exception) {
            throw new \Exception('missformatted string ratio');
        }
        $part = self::$columnSystem / ($leftRatio+$rightRation);
        $this->leftBootstrapRatio = $leftRatio*$part;
        $this->rightBootstrapRatio = $rightRation*$part;
    }
}