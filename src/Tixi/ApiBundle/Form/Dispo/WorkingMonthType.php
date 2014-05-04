<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 09:41
 */

namespace Tixi\ApiBundle\Form\Dispo;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;
use Tixi\ApiBundle\Interfaces\Dispo\WorkingMonthDTO;

/**
 * Class WorkingMonthType
 * @package Tixi\ApiBundle\Form\Dispo
 */
class WorkingMonthType extends CommonAbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('workingMonthId', 'hidden', array(
            'attr' => array('class' => 'workingMonthId')
        ));

        $builder->add('workingMonthDateString', 'text', array(
            'required' => false,
            'disabled' => true,
            'label' => 'workingmonth.field.date'
        ));

        $builder->add('workingMonthStatus', 'text', array(
            'required' => false,
            'disabled' => true,
            'label' => 'workingmonth.field.status'
        ));

        $builder->add('workingMonthMemo', 'text', array(
            'required' => false,
            'label' => 'workingmonth.field.memo',
        ));

        $builder->add('workingDays', 'collection', array(
            'type' => new WorkingDayType(),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true
        ));

        $builder->add('workingShiftNames', 'collection', array(
            'type' => new WorkingShiftNameType(),
            'label' => false,
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true
        ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'workingMonth';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\WorkingMonthDTO'
        ));
    }
}