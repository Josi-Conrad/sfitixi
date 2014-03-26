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

class DriverType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('isActive', 'checkbox', array(
            'required' => false,
            'label' => 'Ist aktiv'
        ));

        $builder->add('title', 'text', array(
            'label' => 'Anrede'
        ));
        $builder->add('firstname', 'text', array(
            'label' => 'Vorname'
        ));
        $builder->add('lastname', 'text', array(
            'label' => 'Nachname'
        ));
        $builder->add('telephone', 'text', array(
            'label' => 'Telefon-Nr'
        ));
        $builder->add('email', 'text', array(
            'required' => false,
            'label' => 'E-Mail'
        ));
        $builder->add('entryDate', new DatePickerType(), array(
            'required' => false,
            'label' => 'Eintrittsdatum'
        ));
        $builder->add('birthday', new DatePickerType(), array(
            'required' => false,
            'label' => 'Geburtsdatum'
        ));
        $builder->add('extraMinutes', 'integer', array(
            'required' => false,
            'label' => 'Extra Minuten'
        ));
        $builder->add('details', 'textarea', array(
            'required' => false,
            'label' => 'Details'
        ));

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