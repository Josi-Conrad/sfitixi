<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 06.04.14
 * Time: 13:48
 */

namespace Tixi\ApiBundle\Tile\Dispo;


use Tixi\ApiBundle\Tile\AbstractTile;
use Tixi\ApiBundle\Tile\Core\FormControlTile;

class RepeatedAssertionTile extends AbstractTile{

    protected $formId;
    protected $form;

    public function __construct($formId, $form) {
        $this->formId = $formId;
        $this->form = $form;
        $this->add(new FormControlTile($formId));
    }

    public function getViewParameters()
    {
        return array('formId'=>$this->formId, 'form'=>$this->form->createView());
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