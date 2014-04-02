<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 10:55
 */

namespace Tixi\ApiBundle\Tile\Core;


use Symfony\Component\Form\SubmitButton;
use Tixi\ApiBundle\Tile\AbstractTile;

class FormControlTile extends AbstractTile{

    public function __construct($formId) {
        $this->add(new BackButtonTile('Abbrechen'));
        $this->add(new SubmitButtonTile($formId, 'Speichern', SubmitButtonTile::$primaryType));
    }

    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:formcontrol.html.twig';
    }

    public function getName()
    {
        return 'formcontrol';
    }
}