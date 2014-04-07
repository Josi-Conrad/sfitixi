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

        $builder->add('handicaps', 'entity', array(
            'class'     => 'Tixi\CoreDomain\Handicap',
            'property'  =>  'name',
            'expanded'  => true,
            'multiple'  => true,
            'label'     => 'passenger.field.handicap'
        ));

        $builder->add('insurances', 'entity', array(
            'class'     => 'Tixi\CoreDomain\Insurance',
            'property'  =>  'name',
            'expanded'  => true,
            'multiple'  => true,
            'label'     => 'passenger.field.insurance'
        ));

        $builder->add('gotMonthlyBilling', 'checkbox', array(
            'required'  => false,
            'label' => 'passenger.field.monthlybilling'
        ));
        $builder->add('notice', 'textarea', array(
            'required'  => false,
            'label' => 'passenger.field.notice'
        ));

        $builder->add('street', 'text', array(
            'label' => 'address.field.street'
        ));
        $builder->add('postalCode', 'text', array(
            'label' => 'address.field.postalcode'
        ));
        $builder->add('city', 'text', array(
            'label' => 'address.field.city'
        ));
        $builder->add('country', 'text', array(
            'label' => 'address.field.country'
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