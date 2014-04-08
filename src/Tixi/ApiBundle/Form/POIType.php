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

class POIType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options) {

        parent::buildForm($builder,$options);

        $builder->add('name', 'text', array(
            'label' => 'poi.field.name',
            'constraints' => array(
                new NotBlank(array('message'=>'vehicle.nr.not_blank'))
            )
        ));
        $builder->add('department', 'text', array(
            'label' => 'poi.field.department'
        ));
        $builder->add('telephone', 'text', array(
            'label' => 'poi.field.telephone'
        ));

        $builder->add('keywords', 'entity', array(
            'class' => 'Tixi\CoreDomain\POIKeyword',
            'property' => 'name',
            'label' => 'poi.field.keyword'
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
        return 'poi';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\POIRegisterDTO'
        ));
    }
}