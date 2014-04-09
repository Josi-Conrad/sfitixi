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
use Symfony\Component\Validator\Constraints\NotBlank;
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
            'constraints' => array(
                new NotBlank(array('message'=>'vehicle.nr.not_blank'))
            )
        ));
        $builder->add('wheelChairAttendance', 'checkbox', array(
            'label' => 'driver.field.wheelchair'
        ));
        $builder->add('driverCategory', 'entity', array(
            'class' => 'Tixi\CoreDomain\DriverCategory',
            'property' => 'name',
            'label' => 'driver.field.category'
        ));
        $builder->add('entryDate', new DatePickerType(), array(
            'required' => false,
            'label' => 'person.field.entrydate'
        ));
        $builder->add('birthday', new DatePickerType(), array(
            'required' => false,
            'label' => 'person.field.birthday'
        ));
        $builder->add('extraMinutes', 'integer', array(
            'required' => false,
            'label' => 'person.field.extraminutes',
            'pattern' => '^\d+$'
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