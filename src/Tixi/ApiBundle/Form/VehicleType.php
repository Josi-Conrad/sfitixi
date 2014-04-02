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
use Tixi\ApiBundle\Form\Shared\DatePickerType;

class VehicleType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array(
            'label' => 'Fahrzeugname'
        ));
        $builder->add('licenceNumber', 'text', array(
            'label' => 'Kennzeichen'
        ));
        $builder->add('dateOfFirstRegistration', new DatePickerType(), array(
            'label' => 'Inverkehrssetzung'
        ));
        $builder->add('parkingLotNumber', 'integer', array(
            'label' => 'Parkplatzbezeichnung'
        ));
        $builder->add('vehicleCategory', 'entity', array(
            'class' => 'Tixi\CoreDomain\VehicleCategory',
            'property' => 'name',
            'label' => 'Fahrzeugkategorie'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'vehicle';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\VehicleRegisterDTO'
        ));
    }
}