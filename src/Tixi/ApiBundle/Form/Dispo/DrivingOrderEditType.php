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

class DrivingOrderEditType extends CommonAbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');

        $builder->add('pickupDate', 'datePicker', array(
            'attr' => array('title' => 'form.field.title.date'),
            'label' => 'drivingorder.field.anchordate'
        ));

        $builder->add('pickupTime', 'time', array(
            'label' => 'drivingorder.field.pickupTime',
            'input' => 'datetime',
            'widget' => 'single_text',
            'attr' => array(
                'title' => 'form.field.title.datetime',
            ),
            'pattern' => '^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$',
            'constraints' => array(
                new Time(),
            ),
        ));


        $builder->add('lookaheadaddressFrom','addresslookahead', array(
            'label' => 'drivingorder.field.lookaheadaddressFrom',
            'lookahead_id' => 'addressfrom',
            'late_init' => true
        ));

        $builder->add('lookaheadaddressTo','addresslookahead', array(
            'label' => 'drivingorder.field.lookaheadaddressTo',
            'lookahead_id' => 'addressto',
            'late_init' => true
        ));

        $builder->add('zoneName','text',array(
            'label' => 'drivingorder.field.zone',
            'disabled' => true,
            'required' => false
        ));


        $builder->add('additionalTime', 'integer', array(
            'required' => false,
            'label' => 'drivingorder.field.additionalTime'
        ));

        $builder->add('compagnion', 'integer', array(
            'required' => false,
            'label' => 'drivingorder.field.compagnion'
        ));

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