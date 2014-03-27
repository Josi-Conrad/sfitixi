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

class PassengerType extends PersonType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        parent::buildForm($builder,$options);

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