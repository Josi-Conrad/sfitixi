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

class PassengerType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('isActive', 'checkbox', array(
            'required'  => false,
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
            'required'  => false,
            'label' => 'E-Mail'
        ));
        $builder->add('entryDate', 'date', array(
            'label' => 'Eintrittsdatum'
        ));
        $builder->add('birthday', 'date', array(
            'label' => 'Geburtsdatum'
        ));
        $builder->add('extraMinutes', 'integer', array(
            'required'  => false,
            'label' => 'Extra Minuten'
        ));
        $builder->add('details', 'textarea', array(
            'required'  => false,
            'label' => 'Details'
        ));

        $builder->add('handicap', 'entity', array(
            'class' => 'Tixi\CoreDomain\Handicap',
            'property' => 'name',
            'label' => 'Behinderung'
        ));
        $builder->add('isOverweight', 'checkbox', array(
            'required'  => false,
            'label' => 'Ist Übergewichtig'
        ));
        $builder->add('gotMonthlyBilling', 'checkbox', array(
            'required'  => false,
            'label' => 'Monatliche Rechnung?'
        ));
        $builder->add('notice', 'textarea', array(
            'required'  => false,
            'label' => 'Notizen zum Fahrgast'
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
        return 'passenger';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\PassengerRegisterDTO'
        ));
    }
}