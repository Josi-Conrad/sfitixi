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
 * Class VehicleDepotType
 * @package Tixi\ApiBundle\Form\Management
 */
class VehicleDepotType extends CommonAbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array(
            'label' => 'vehicledepot.field.name',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));

        $builder->add('street', 'text', array(
            'label' => 'address.field.street',
            'attr'=>array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message'=>'field.not_blank'))
            ),
        ));
        $builder->add('postalCode', 'text', array(
            'label' => 'address.field.postalcode',
            'pattern' => '^[\+0-9A-Z]{4,7}',
            'attr'=>array('title' => 'form.field.title.postalcode'),
            'constraints' => array(
                new NotBlank(array('message'=>'field.not_blank'))
            ),
        ));
        $builder->add('city', 'text', array(
            'label' => 'address.field.city',
            'attr'=>array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message'=>'field.not_blank'))
            ),
        ));
        $builder->add('country', 'text', array(
            'label' => 'address.field.country',
            'attr'=>array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message'=>'field.not_blank'))
            ),
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Management\VehicleDepotRegisterDTO'
        ));
    }
} 