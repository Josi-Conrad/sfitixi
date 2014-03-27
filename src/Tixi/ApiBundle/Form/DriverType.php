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
use Tixi\ApiBundle\Form\Shared\DatePickerType;

class DriverType extends PersonType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        parent::buildForm($builder,$options);

        $builder->add('licenseNumber', 'text', array(
            'label' => 'Fahrausweis-Nummer'
        ));
        $builder->add('wheelChairAttendance', 'checkbox', array(
            'required' => false,
            'label' => 'Rollstuhlfahrten'
        ));

        $builder->add('driverCategory', 'entity', array(
            'class' => 'Tixi\CoreDomain\DriverCategory',
            'property' => 'name',
            'label' => 'Fahrer-Kategorie'
        ));

        $builder->add('street', 'text', array(
            'label' => 'Strasse / Nummer'
        ));
        $builder->add('postalCode', 'text', array(
            'label' => 'PLZ'
        ));
        $builder->add('city', 'text', array(
            'label' => 'Ort'
        ));
        $builder->add('country', 'text', array(
            'label' => 'Land'
        ));

        $builder->add('save', 'submit', array(
            'label' => 'Speichern'
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