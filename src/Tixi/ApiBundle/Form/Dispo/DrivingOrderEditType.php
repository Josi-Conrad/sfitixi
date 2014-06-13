<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 07.06.14
 * Time: 18:36
 */

namespace Tixi\ApiBundle\Form\Dispo;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Time;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;
use Tixi\ApiBundle\Form\Shared\DrivingOrderTime;
use Tixi\CoreDomain\Dispo\DrivingOrder;

/**
 * Class DrivingOrderEditType
 * @package Tixi\ApiBundle\Form\Dispo
 */
class DrivingOrderEditType extends CommonAbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');

        $builder->add('pickupDate', 'text', array(
            'label' => 'drivingorder.field.anchordate',
            'disabled' => true,
            'required' => false
        ));

        $builder->add('pickupTime', 'text', array(
            'label' => 'drivingorder.field.pickupTime',
            'disabled' => true,
            'required' => false
        ));

        $builder->add('lookaheadaddressFrom', 'text', array(
            'label' => 'drivingorder.field.lookaheadaddressFrom',
            'disabled' => true,
            'required' => false
        ));

        $builder->add('lookaheadaddressTo', 'text', array(
            'label' => 'drivingorder.field.lookaheadaddressTo',
            'disabled' => true,
            'required' => false
        ));


        $builder->add('zoneName','text',array(
            'label' => 'drivingorder.field.zone',
            'disabled' => true,
            'required' => false
        ));


        $builder->add('additionalTime', 'text', array(
            'required' => false,
            'label' => 'drivingorder.field.additionalTime',
            'disabled' => true,
        ));

        $builder->add('compagnion', 'text', array(
            'required' => false,
            'label' => 'drivingorder.field.compagnion',
            'disabled' => true,
        ));

        $builder->add('orderStatus', 'choice', array(
                'label' => 'drivingorder.field.status',
                'choices' => DrivingOrder::getStatusArray()
            )
        );

        $builder->add('memo', 'textarea', array(
            'required' => false,
            'label' => 'drivingorder.field.memo'
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\DrivingOrderEditDTO'
        ));
    }
} 