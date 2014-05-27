<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 12:41
 */

namespace Tixi\ApiBundle\Tile\Dispo;


use Tixi\ApiBundle\Tile\AbstractTile;
use Tixi\ApiBundle\Tile\Core\FormControlTile;

/**
 * Class ProductionPlanEditTile
 * @package Tixi\ApiBundle\Tile\Dispo
 */
class ProductionPlanEditTile extends AbstractTile{

    protected $formId;
    protected $form;
    protected $isStandalone;

    /**
     * @param $form
     * @param bool $isStandalone
     */
    public function __construct($form, $isStandalone=true) {
        $this->formId = $form->getName();
        $this->form = $form;
        $this->isStandalone = $isStandalone;
        $this->add(new FormControlTile($this->formId));
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('formId'=>$this->formId, 'isStandalone'=>$this->isStandalone, 'form'=>$this->form->createView());
    }

    /**
     * @return mixed
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:productionplanedit.html.twig';
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return 'productionPlanEditForm';
    }
}