<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 09:31
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

/**
 * Class AbstractFormViewTile
 * @package Tixi\ApiBundle\Tile\Core
 */
abstract class AbstractFormViewTile extends AbstractTile{

    protected $basicFormRows=array();
    protected $expandedFormRows=array();
    protected $isStandalone;
    protected $formViewId;

    protected $dto;

    /**
     * @param $formViewId
     * @param $dto
     * @param $editPath
     * @param bool $isStandalone
     */
    public function __construct($formViewId, $dto, $editPath, $isStandalone=false) {
        $this->dto = $dto;
        $this->isStandalone = $isStandalone;
        $this->formViewId = $formViewId;
        $this->add(new FormViewControlTile($editPath));
        $this->createFormRows();
    }

    /**
     * @return mixed
     */
    public abstract function createFormRows();

    /**
     * @return array
     */
    public function getViewParameters() {
        return array('basicFormRows'=>$this->basicFormRows, 'expandedFormRows'=>$this->expandedFormRows, 'isStandalone'=>$this->isStandalone, 'formViewId'=>$this->formViewId);
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:formview.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'formview';
    }
}