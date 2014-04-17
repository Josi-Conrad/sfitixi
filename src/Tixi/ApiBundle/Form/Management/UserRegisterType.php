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
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class UserRegisterType
 * @package Tixi\ApiBundle\Form\Management
 */
class UserRegisterType extends AbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('role', 'entity', array(
            'class' => 'Tixi\SecurityBundle\Entity\Role',
            'property' => 'name',
            'label' => 'user.field.role'
        ));
        $builder->add('username', 'text', array(
            'label' => 'user.field.username',
            'attr'=>array('title' => 'form.field.title.letter_digit'),
            'pattern' => '^[a-zA-Z\d]+$',
            'constraints' => array(
                new NotBlank(array('message'=>'user.name.not_blank'))
            ),
        ));
        $builder->add('password', 'password', array(
            'label' => 'user.field.password',
            'constraints' => array(
                new NotBlank(array('message'=>'user.password.not_blank'))
            ),
        ));
        $builder->add('email', 'email', array(
            'label' => 'user.field.email',
            'constraints' => array(
                new NotBlank(array('message'=>'user.email.not_blank'))
            ),
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Management\UserRegisterDTO'
        ));
    }
}