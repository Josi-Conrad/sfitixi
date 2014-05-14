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
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;
use Tixi\ApiBundle\Form\Shared\Lookahead\AddressLookaheadType;
use Tixi\ApiBundle\Form\Shared\TelephoneType;

/**
 * Class PersonType
 * @package Tixi\ApiBundle\Form
 */
class PersonType extends CommonAbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('gender', 'choice', array(
            'label' => 'person.field.gender',
            'choices' => array(
                'm' => 'person.gender.male',
                'f' => 'person.gender.female'),
            'multiple' => false,
            'expanded' => true,
        ));

        $builder->add('title', 'text', array(
            'label' => 'person.field.title',
            'required' => false,
        ));

        $builder->add('firstname', 'text', array(
            'label' => 'person.field.firstname',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('lastname', 'text', array(
            'label' => 'person.field.lastname',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('telephone', 'telephoneType', array(
            'label' => 'person.field.telephone',
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
//        $builder->add('street', 'text', array(
//            'label' => 'address.field.street',
//            'attr' => array('title' => 'form.field.title.not_blank'),
//            'constraints' => array(
//                new NotBlank(array('message' => 'field.not_blank'))
//            ),
//        ));
//        $builder->add('postalCode', 'text', array(
//            'label' => 'address.field.postalcode',
//            'pattern' => '^[\+0-9A-Z]{4,7}',
//            'attr' => array('title' => 'form.field.title.postalcode'),
//            'constraints' => array(
//                new NotBlank(array('message' => 'field.not_blank'))
//            ),
//        ));
//        $builder->add('city', 'text', array(
//            'label' => 'address.field.city',
//            'attr' => array('title' => 'form.field.title.not_blank'),
//            'constraints' => array(
//                new NotBlank(array('message' => 'field.not_blank'))
//            ),
//        ));
//        $builder->add('country', 'text', array(
//            'label' => 'address.field.country',
//            'attr' => array('title' => 'form.field.title.not_blank'),
//            'constraints' => array(
//                new NotBlank(array('message' => 'field.not_blank'))
//            ),
//        ));
    }
}