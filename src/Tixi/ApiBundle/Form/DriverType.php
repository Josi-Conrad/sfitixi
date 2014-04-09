<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */

namespace Tixi\ApiBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Tixi\ApiBundle\Form\Shared\DatePickerType;

class DriverType extends PersonType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        parent::buildForm($builder,$options);

        $builder->add('email', 'email', array(
            'required' => false,
            'label' => 'person.field.email'
        ));
        $builder->add('licenceNumber', 'text', array(
            'label' => 'driver.field.licence',
            'attr'=>array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message'=>'form.field.not_blank'))
            ),
        ));
        $builder->add('wheelChairAttendance', 'checkbox', array(
            'required' => false,
            'label' => 'driver.field.wheelchair'
        ));
        $builder->add('driverCategory', 'entity', array(
            'class' => 'Tixi\CoreDomain\DriverCategory',
            'property' => 'name',
            'label' => 'driver.field.category',
            'attr'=>array('title' => 'form.field.title.not_selected'),
            'constraints' => array(
                new NotBlank(array('message'=>'form.field.not_blank'))
            ),
        ));
        $builder->add('entryDate', new DatePickerType(), array(
            'required' => false,
            'label' => 'person.field.entrydate',
            'attr'=>array('title' => 'form.field.title.date'),
            'pattern' => '^(0[1-9]|[1|2][0-9]|3[0|1]).(0[1-9]|1[0|1|2]).(19|20)\d\d$',
            'constraints' => array(
                new DateTime(),
            ),
        ));
        $builder->add('birthday', new DatePickerType(), array(
            'required' => false,
            'label' => 'person.field.birthday',
            'attr'=>array('title' => 'form.field.title.date'),
            'pattern' => '^(0[1-9]|[1|2][0-9]|3[0|1]).(0[1-9]|1[0|1|2]).(19|20)\d\d$',
            'constraints' => array(
                new DateTime(),
            ),
        ));
        $builder->add('extraMinutes', 'integer', array(
            'required' => false,
            'label' => 'person.field.extraminutes',
            'attr'=>array('title' => 'form.field.title.digit'),
            'constraints' => array(
                new Regex(array('message'=>'form.field.title.digit','pattern'=>'/\d+/'))
            ),
        ));
        $builder->add('details', 'textarea', array(
            'required' => false,
            'label' => 'person.field.details'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'driver';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\DriverRegisterDTO'
        ));
    }
}