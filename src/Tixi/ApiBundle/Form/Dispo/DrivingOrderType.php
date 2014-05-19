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
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;

/**
 * Class DrivingOrderType
 * @package Tixi\ApiBundle\Form\Dispo
 */
class DrivingOrderType extends CommonAbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');

        $builder->add('anchorDate', 'datePicker', array(
            'attr' => array('title' => 'form.field.title.date'),
            'label' => 'drivingorder.field.anchordate'
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