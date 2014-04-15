<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 14.04.14
 * Time: 15:20
 */

namespace Tixi\ApiBundle\Form\Shared;


use Symfony\Component\Form\AbstractType;

abstract class CommonAbstractType extends AbstractType{

    protected $menuId;

    public function __construct($menuId) {
        $this->menuId = $menuId;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'fpw_'.$this->menuId;
    }

} 