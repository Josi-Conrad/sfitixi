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
use Tixi\ApiBundle\Interfaces\Dispo\ShiftSelectionType;

class RepeatedDrivingAssertionType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('memo', 'text', array(
            'label' => 'Memo'
        ));
        $builder->add('anchorDate', 'date', array(
            'label' => 'Ab'
        ));
        $builder->add('endDate', 'date', array(
            'label' => 'Bis'
        ));
        $builder->add('frequency', 'choice', array(
            'choices' => array('weekly'=>'Wöchentlich','monthly'=>'Monatlich'),
        ));
        $builder->add('withHolidays','checkbox',array(
            'label' => 'inklusive Feiertage'
        ));

        //weekly part
        $builder->add('weeklyDaysSelector', 'choice', array(
            'choices' => array('Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag','Sonday'=>'Sonntag'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'Wochentag(e)'
        ));
        $builder->add('weeklyShiftSelections', 'collection', array(
            'type' => new ShiftSelectionType(),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false
        ));

        //monthly part
        $builder->add('monthlyFirstWeeklySelector', 'choice', array(
            'choices' => array('Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag','Sonday'=>'Sonntag'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'Erste'
        ));
        $builder->add('monthlySecondWeeklySelector', 'choice', array(
            'choices' => array('Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag','Sonday'=>'Sonntag'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'Zweite'
        ));
        $builder->add('monthlyThirdWeeklySelector', 'choice', array(
            'choices' => array('Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag','Sonday'=>'Sonntag'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'Dritte'
        ));
        $builder->add('monthlyFourthWeeklySelector', 'choice', array(
            'choices' => array('Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag','Sonday'=>'Sonntag'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'Vierte'
        ));
        $builder->add('monthlyLastWeeklySelector', 'choice', array(
            'choices' => array('Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag','Sonday'=>'Sonntag'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'Letzte'
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