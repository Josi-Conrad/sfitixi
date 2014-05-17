<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.05.14
 * Time: 20:17
 */

namespace Tixi\ApiBundle\Tile\Dispo;


use Tixi\ApiBundle\Tile\AbstractTile;
use Tixi\ApiBundle\Tile\Core\FormControlTile;

class DrivingOrderTile extends AbstractTile{

    protected $formId;
    protected $form;
    protected $frequency;

    /**
     * @param $form
     * @param string $frequency
     */
    public function __construct($form, $frequency='') {
        $this->formId = $form->getName();
        $this->form = $form;
        $this->add(new FormControlTile($this->formId));
    }

    /**
     * @return array
     */
    public function getViewParameters()
    {
        return array('formId'=>$this->formId, 'form'=>$this->form->createView());
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:drivingorder.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'drivingOrderForm';
    }
} 