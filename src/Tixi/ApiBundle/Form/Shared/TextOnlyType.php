<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 10.04.14
 * Time: 08:58
 */

namespace Tixi\ApiBundle\Form\Shared;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Will be set with <span> in form-theme
 * Class TextOnlyType
 * @package Tixi\ApiBundle\Form\Shared
 */
class TextOnlyType extends AbstractType {
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'label' => false,
            'required' => false,
        ));
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent() {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'textOnly';
    }
}