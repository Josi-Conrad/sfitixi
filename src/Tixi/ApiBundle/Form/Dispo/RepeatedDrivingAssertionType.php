<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 01:01
 */

namespace Tixi\ApiBundle\Form\Dispo;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tixi\ApiBundle\Form\Shared\DatePickerType;

class RepeatedDrivingAssertionType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('memo', 'text', array(
            'label' => 'repeateddrivingmission.field.memo'
        ));
        $builder->add('anchorDate', new DatePickerType(), array(
            'label' => 'repeateddrivingmission.field.anchordate'
        ));
        $builder->add('endDate', new DatePickerType(), array(
            'label' => 'repeateddrivingmission.field.endDate'
        ));
        $builder->add('frequency', 'choice', array(
            'choices' => array('weekly'=>'repeateddrivingmission.field.frequency.weekly','monthly'=>'repeateddrivingmission.field.frequency.monthly'),
            'label' => ' '
        ));
        $builder->add('withHolidays','checkbox',array(
            'label' => 'repeateddrivingmission.field.withHolidays'
        ));

        //weekly part
        $builder->add('weeklyDaysSelector', 'choice', array(
            'choices' => array('monday'=>'monday.name','tuesday'=>'tuesday.name','wednesday'=>'wednesday.name','thursday'=>'thursday.name','friday'=>'friday.name','saturday'=>'saturday.name','sunday'=>'sunday.name'),
            'multiple' => true,
            'expanded' => true
        ));
        $builder->add('weeklyShiftSelections', 'collection', array(
            'type' => new ShiftSelectionType(),
            'allow_add' => true,
            'allow_delete' => true
        ));

        //monthly part
        $builder->add('monthlyFirstWeeklySelector', 'choice', array(
            'choices' => array('monday'=>'monday.abbreviation.name','tuesday'=>'tuesday.abbreviation.name','wednesday'=>'wednesday.abbreviation.name','thursday'=>'thursday.abbreviation.name','friday'=>'friday.abbreviation.name','saturday'=>'saturday.abbreviation.name','sunday'=>'sunday.abbreviation.name'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'firstweek.name'
        ));
        $builder->add('monthlySecondWeeklySelector', 'choice', array(
            'choices' => array('monday'=>'monday.abbreviation.name','tuesday'=>'tuesday.abbreviation.name','wednesday'=>'wednesday.abbreviation.name','thursday'=>'thursday.abbreviation.name','friday'=>'friday.abbreviation.name','saturday'=>'saturday.abbreviation.name','sunday'=>'sunday.abbreviation.name'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'secondweek.name'
        ));
        $builder->add('monthlyThirdWeeklySelector', 'choice', array(
            'choices' => array('monday'=>'monday.abbreviation.name','tuesday'=>'tuesday.abbreviation.name','wednesday'=>'wednesday.abbreviation.name','thursday'=>'thursday.abbreviation.name','friday'=>'friday.abbreviation.name','saturday'=>'saturday.abbreviation.name','sunday'=>'sunday.abbreviation.name'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'thirdweek.name'
        ));
        $builder->add('monthlyFourthWeeklySelector', 'choice', array(
            'choices' => array('monday'=>'monday.abbreviation.name','tuesday'=>'tuesday.abbreviation.name','wednesday'=>'wednesday.abbreviation.name','thursday'=>'thursday.abbreviation.name','friday'=>'friday.abbreviation.name','saturday'=>'saturday.abbreviation.name','sunday'=>'sunday.abbreviation.name'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'fourthweek.name'
        ));
        $builder->add('monthlyLastWeeklySelector', 'choice', array(
            'choices' => array('monday'=>'monday.abbreviation.name','tuesday'=>'tuesday.abbreviation.name','wednesday'=>'wednesday.abbreviation.name','thursday'=>'thursday.abbreviation.name','friday'=>'friday.abbreviation.name','saturday'=>'saturday.abbreviation.name','sunday'=>'sunday.abbreviation.name'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'lastweek.name'
        ));
        $builder->add('monthlyShiftSelections', 'collection', array(
            'type' => new ShiftSelectionType(),
            'allow_add' => true,
            'allow_delete' => true
        ));

    }
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'repeatedDrivingAssertion';

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\RepeatedDrivingAssertionRegisterDTO'
        ));
    }
}