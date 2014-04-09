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

class UserType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');

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
            'required' => false,
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\UserRegisterDTO'
        ));
    }
}