<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.03.14
 * Time: 20:41
 */

namespace Tixi\ApiBundle\Tile\Dispo;


use Tixi\ApiBundle\Tile\AbstractTile;
use Tixi\ApiBundle\Tile\Core\FormControlTile;

/**
 * Class WorkingMonthTile
 * @package Tixi\ApiBundle\Tile\Dispo
 */
class WorkingMonthTile extends AbstractTile{

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
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:workingmonth.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'form';
    }
}