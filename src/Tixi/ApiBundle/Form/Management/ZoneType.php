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
 * Class ZoneType
 * @package Tixi\ApiBundle\Form\Management
 */
class ZoneType extends CommonAbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array(
            'label' => 'zone.field.name',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('rate', 'money', array(
            'label' => 'zone.field.rate',
            'precision' => 2,
            'currency' => false,
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Management\ZoneRegisterDTO'
        ));
    }
} 