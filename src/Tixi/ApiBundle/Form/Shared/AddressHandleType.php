<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 29.04.14
 * Time: 11:09
 */

namespace Tixi\ApiBundle\Form\Shared;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddressHandleType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id','hidden');
        $builder->add('displayName', 'hidden');
        $builder->add('street', 'text', array(
            'label' => 'address.field.street',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('postalCode', 'text', array(
            'label' => 'address.field.postalcode',
            'pattern' => '^[\+0-9A-Z]{4,7}',
            'attr' => array('title' => 'form.field.title.postalcode'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('city', 'text', array(
            'label' => 'address.field.city',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('country', 'text', array(
            'label' => 'address.field.country',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('lat', 'text', array(
            'label' => 'address.field.lat',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            )
        ));
        $builder->add('lng', 'text', array(
            'label' => 'address.field.lng',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            )
        ));
        $builder->add('source','hidden');


    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\App\AppBundle\Interfaces\AddressHandleDTO',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'addresshandle';
    }
}