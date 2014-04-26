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
 * Class TelephoneType
 * @package Tixi\ApiBundle\Form\Shared
 */
class TelephoneType extends AbstractType {
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'pattern' => '^[\+0-9 ()*-]{3,19}',
            'attr' => array('title' => 'form.field.title.telephone'),
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
        return 'telephoneType';
    }
}