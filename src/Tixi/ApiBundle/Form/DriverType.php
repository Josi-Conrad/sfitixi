<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.02.14
 * Time: 23:05
 */

namespace Tixi\ApiBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DriverType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('isActive', 'checkbox', array(
            'required'  => false,
        ));

        $builder->add('title', 'text');
        $builder->add('firstname', 'text');
        $builder->add('lastname', 'text');
        $builder->add('telephone', 'text');
        $builder->add('email', 'text');
        $builder->add('entryDate', 'date');
        $builder->add('birthday', 'date');
        $builder->add('extraMinutes', 'integer', array(
            'required'  => false,
        ));
        $builder->add('details', 'textarea', array(
            'required'  => false,
        ));

        $builder->add('licenseNumber', 'text');
        $builder->add('wheelChairAttendance', 'checkbox', array(
            'required'  => false,
        ));

        $builder->add('driverCategory', 'entity', array(
            'class' => 'Tixi\CoreDomain\DriverCategory',
            'property' => 'name'
        ));

        $builder->add('street', 'text');
        $builder->add('postalCode', 'text');
        $builder->add('city', 'text');
        $builder->add('country', 'text');

        $builder->add('save', 'submit');
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