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
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Tixi\ApiBundle\Form\Shared\TelephoneType;

/**
 * Class PersonType
 * @package Tixi\ApiBundle\Form
 */
class PersonType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('title', 'choice', array(
            'label' => 'person.field.title',
            'choices' => array(
                'm' => 'person.title.male',
                'f' => 'person.title.female'),
            'multiple' => false,
            'expanded' => true,
        ));
        $builder->add('firstname', 'text', array(
            'label' => 'person.field.firstname',
            'attr'=>array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message'=>'field.not_blank'))
            ),
        ));
        $builder->add('lastname', 'text', array(
            'label' => 'person.field.lastname',
            'attr'=>array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message'=>'field.not_blank'))
            ),
        ));
        $builder->add('telephone', new TelephoneType(), array(
            'label' => 'person.field.telephone',
        ));
        $builder->add('street', 'text', array(
            'label' => 'address.field.street',
            'attr'=>array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message'=>'field.not_blank'))
            ),
        ));
        $builder->add('postalCode', 'text', array(
            'label' => 'address.field.postalcode',
            'pattern' => '^[\+0-9A-Z]{4,7}',
            'attr'=>array('title' => 'form.field.title.postalcode'),
            'constraints' => array(
                new NotBlank(array('message'=>'field.not_blank'))
            ),
        ));
        $builder->add('city', 'text', array(
            'label' => 'address.field.city',
            'attr'=>array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message'=>'field.not_blank'))
            ),
        ));
        $builder->add('country', 'text', array(
            'label' => 'address.field.country',
            'attr'=>array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message'=>'field.not_blank'))
            ),
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