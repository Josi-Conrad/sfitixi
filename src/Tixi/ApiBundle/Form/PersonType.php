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

class PersonType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('isActive', 'checkbox', [
            'data' => true,
            'required' => false,
            'label' => 'Ist aktiv',
        ]);

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
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'person';
    }
}