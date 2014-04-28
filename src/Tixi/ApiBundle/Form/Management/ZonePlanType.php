<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 28.04.14
 * Time: 09:39
 */

namespace Tixi\ApiBundle\Form\Management;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;

/**
 * Class ZonePlanType
 * @package Tixi\ApiBundle\Form\Management
 */
class ZonePlanType extends CommonAbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('innerZone', 'textarea', array(
            'label' => 'zoneplan.field.innerzone',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));

        $builder->add('adjacentZone', 'textarea', array(
            'label' => 'zoneplan.field.adjacentzone',
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
            'data_class' => 'Tixi\ApiBundle\Interfaces\Management\ZonePlanDTO'
        ));
    }
} 