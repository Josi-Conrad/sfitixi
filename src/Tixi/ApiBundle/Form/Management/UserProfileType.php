<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.02.14
 * Time: 23:05
 */

namespace Tixi\ApiBundle\Form\Management;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserProfileType
 * @package Tixi\ApiBundle\Form\Management
 */
class UserProfileType extends AbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');

        $builder->add('email', 'email', array(
            'label' => 'user.field.email',
            'constraints' => array(
                new NotBlank(array('message'=>'user.email.not_blank'))
            ),
        ));

        $builder->add('password', 'password', array(
            'label' => 'user.field.actual_password',
            'constraints' => array(
                new NotBlank(array('message'=>'user.password.not_blank')),
                new UserPassword(array('message'=>'user.password.wrong'))
            ),
        ));
        $builder->add('new_password', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'user.password.not_match',
            'required' => false,
            'first_options'  => array('label' => 'user.field.new_password'),
            'second_options' => array('label' => 'user.field.new_password_2'),
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'userprofile';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Management\UserProfileDTO'
        ));
    }
}