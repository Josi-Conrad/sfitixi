<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 09.04.14
 * Time: 17:18
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class DeleteButtonTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class DeleteButtonTile extends AbstractTile{

    protected $buttonId;
    protected $displayText;
    protected $targetSrc;
    protected $deleteConfirmText;

    /**
     * @param $buttonId
     * @param $targetSrc
     * @param $displayText
     * @param string $deleteConfirmText
     */
    public function __construct($buttonId, $targetSrc, $displayText, $deleteConfirmText='delete.logically.standardtext') {
        $this->buttonId = $buttonId;
        $this->targetSrc = $targetSrc;
        $this->displayText = $displayText;
        $this->deleteConfirmText = $deleteConfirmText;
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('buttonId'=>$this->buttonId, 'targetSrc'=>$this->targetSrc, 'displayText'=>$this->displayText, 'deleteConfirmText'=>$this->deleteConfirmText);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:deletebutton.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'deletebutton';
    }
}