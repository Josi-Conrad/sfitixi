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

class RepeatedMonthlyDrivingAssertionType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('firstWeeklySelector', 'choice', array(
            'choices' => array('Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag','Sonday'=>'Sonntag'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'Erste'
        ));
        $builder->add('secondWeeklySelector', 'choice', array(
            'choices' => array('Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag','Sonday'=>'Sonntag'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'Zweite'
        ));
        $builder->add('thirdWeeklySelector', 'choice', array(
            'choices' => array('Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag','Sonday'=>'Sonntag'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'Dritte'
        ));
        $builder->add('fourthWeeklySelector', 'choice', array(
            'choices' => array('Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag','Sonday'=>'Sonntag'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'Vierte'
        ));
        $builder->add('lastWeeklySelector', 'choice', array(
            'choices' => array('Monday'=>'Montag','Tuesday'=>'Dienstag','Wednesday'=>'Mittwoch','Thursday'=>'Donnerstag','Friday'=>'Freitag','Saturday'=>'Samstag','Sonday'=>'Sonntag'),
            'multiple' => true,
            'expanded' => true,
            'label' => 'Letzte'
        ));
        $builder->add('shiftSelections', 'collection', array(
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
        return 'repeadedMonthlySelection';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\RepeatedMonthlyDrivingAssertionRegisterDTO'
        ));
    }
}