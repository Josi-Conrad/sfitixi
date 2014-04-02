<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 09:31
 */

namespace Tixi\ApiBundle\Tile\Core;


use Tixi\ApiBundle\Tile\AbstractTile;

abstract class AbstractFormViewTile extends AbstractTile{

    protected $basicFormRows=array();
    protected $expandedFormRows=array();
    protected $isStandalone;

    protected $dto;

    public function __construct($dto, $editPath, $isStandalone=false) {
        $this->dto = $dto;
        $this->isStandalone = $isStandalone;
        $this->add(new FormViewControlTile($editPath));
        $this->createFormRows();
    }

    public abstract function createFormRows();

    public function getViewParameters() {
        return array('basicFormRows'=>$this->basicFormRows, 'expandedFormRows'=>$this->expandedFormRows, 'isStandalone'=>$this->isStandalone);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:formview.html.twig';
    }

    public function getName()
    {
        return 'formview';
    }
}