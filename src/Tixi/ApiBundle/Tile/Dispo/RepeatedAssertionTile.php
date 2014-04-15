<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 09:57
 */

namespace Tixi\ApiBundle\Tile\Dispo;


use Tixi\ApiBundle\Tile\AbstractTile;
use Tixi\ApiBundle\Tile\Core\FormControlTile;
use Tixi\ApiBundle\Tile\Core\FormTile;

class RepeatedAssertionTile extends AbstractTile{

    protected $formId;
    protected $form;
    protected $frequency;

    public function __construct($form, $frequency='') {
        $this->formId = $form->getName();
        $this->form = $form;
        $this->frequency = $frequency;
        $this->add(new FormControlTile($this->formId));
    }

    public function getViewParameters()
    {
        return array('formId'=>$this->formId, 'form'=>$this->form->createView(), 'frequency'=>$this->frequency);
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:repeatedassertion.html.twig';
    }

    public function getName()
    {
        return 'repeatedAssertionForm';
    }
}