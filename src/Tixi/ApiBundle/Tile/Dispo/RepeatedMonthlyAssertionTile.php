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

class RepeatedMonthlyAssertionTile extends AbstractTile{

    protected $formId;
    protected $monthlyAssertionForm;

    public function __construct($formId, $monthlyAssertionForm) {
        $this->formId = $formId;
        $this->monthlyAssertionForm = $monthlyAssertionForm;
        $this->add(new FormControlTile($formId));
    }

    public function getViewParameters()
    {
        return array('formId'=>$this->formId, 'form'=>$this->monthlyAssertionForm->createView());
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:repeatedmonthlyassertion.html.twig';
    }

    public function getName()
    {
        return 'repeatedMonthlyAssertionForm';
    }
}