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

/**
 * Class FormControlTile
 * @package Tixi\ApiBundle\Tile\Core
 */
class FormControlTile extends AbstractTile{
    /**
     * @param $formId
     */
    public function __construct($formId) {
        $this->add(new BackButtonTile('button.back'));
        $this->add(new SubmitButtonTile($formId, 'button.save', SubmitButtonTile::$primaryType));
    }

    /**
     * @return mixed|string
     */
    public function getTemplateName()
    {
        return 'TixiApiBundle:Tile:formcontrol.html.twig';
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'formcontrol';
    }
}