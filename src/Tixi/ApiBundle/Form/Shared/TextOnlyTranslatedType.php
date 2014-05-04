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
 * Will be set with <span> and |trans for value in form-theme
 * Class TextOnlyTranslatedType
 * @package Tixi\ApiBundle\Form\Shared
 */
class TextOnlyTranslatedType extends AbstractType {
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'label' => false,
            'required' => false,
        ));
    }

    public function getParent() {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'textOnlyTranslated';
    }
}