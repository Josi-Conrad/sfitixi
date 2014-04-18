<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.04.14
 * Time: 09:39
 */

namespace Tixi\ApiBundle\Form\Management;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;

/**
 * Class HandicapType
 * @package Tixi\ApiBundle\Form\Management
 */
class HandicapType extends CommonAbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array(
            'label' => 'handicap.field.name',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'handicap.name.not_blank'))
            ),
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Management\HandicapRegisterDTO'
        ));
    }
} 