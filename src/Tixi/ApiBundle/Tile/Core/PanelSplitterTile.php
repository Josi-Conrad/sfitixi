<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 01.04.14
 * Time: 15:27
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

class PanelSplitterTile extends AbstractTile{

    protected static $columnSystem = 12;

    protected $leftTile;
    protected $rightTile;

    protected $leftBootstrapRatio;
    protected $rightBootstrapRatio;

    public function addLeft(AbstractTile $toAdd) {
        return $this->leftTile->add($toAdd);
    }

    public function addRight(AbstractTile $toAdd) {
        return $this->rightTile->add($toAdd);
    }

    public function __construct($splitRatio='1:1') {
        $this->leftTile = $this->add(new PanelSplitterLeftTile());
        $this->rightTile = $this->add(new PanelSplitterRightTile());
        $this->setBootstrapRatio($splitRatio);
    }

    public function getViewParameters() {
        return array('leftRatio'=>$this->leftBootstrapRatio, 'rightRatio'=>$this->rightBootstrapRatio);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:panelsplitter.html.twig';
    }

    public function getName()
    {
        return 'panelsplitter';
    }

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