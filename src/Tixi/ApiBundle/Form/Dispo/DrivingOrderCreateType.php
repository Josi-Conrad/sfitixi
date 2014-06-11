<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 17.05.14
 * Time: 20:36
 */

namespace Tixi\ApiBundle\Form\Dispo;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Time;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;
use Tixi\ApiBundle\Form\Shared\DrivingOrderTime;

/**
 * Class DrivingOrderType
 * @package Tixi\ApiBundle\Form\Dispo
 */
class DrivingOrderCreateType extends CommonAbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');

        $builder->add('anchorDate', 'datePicker', array(
            'attr' => array('title' => 'form.field.title.date'),
            'label' => 'drivingorder.field.anchordate'
        ));

        $builder->add('endDate', 'datePicker', array(
            'required' => false,
            'label' => 'drivingorder.field.enddate'
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

        $builder->add('orderTime', new DrivingOrderTime(), array(
            'required' => false,
        ));

        $builder->add('isRepeated', 'checkbox', array(
            'required' => false,
            'label' => 'drivingorder.field.isRepeated'
        ));

        $builder->add('withHolidays', 'checkbox', array(
            'required' => false,
            'label' => 'repeateddrivingorder.field.withHolidays'
        ));

        $builder->add('mondayOrderTime', new DrivingOrderTime('1'), array(
            'required' => false,
            'label' => 'monday.name'
        ));
        $builder->add('tuesdayOrderTime', new DrivingOrderTime('2'), array(
            'required' => false,
            'label' => 'tuesday.name'
        ));
        $builder->add('wednesdayOrderTime', new DrivingOrderTime('3'), array(
            'required' => false,
            'label' => 'wednesday.name'
        ));
        $builder->add('thursdayOrderTime', new DrivingOrderTime('4'), array(
            'required' => false,
            'label' => 'thursday.name'
        ));
        $builder->add('fridayOrderTime', new DrivingOrderTime('5'), array(
            'required' => false,
            'label' => 'friday.name'
        ));
        $builder->add('saturdayOrderTime', new DrivingOrderTime('6'), array(
            'required' => false,
            'label' => 'saturday.name'
        ));
        $builder->add('sundayOrderTime', new DrivingOrderTime('7'), array(
            'required' => false,
            'label' => 'sunday.name'
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
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\DrivingOrderRegisterDTO'
        ));
    }
} 