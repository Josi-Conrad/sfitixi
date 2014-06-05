<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.02.14
 * Time: 23:05
 */

namespace Tixi\ApiBundle\Form\Management;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tixi\SecurityBundle\Entity\Role;

/**
 * Class UserEditType
 * @package Tixi\ApiBundle\Form\Management
 */
class UserEditType extends AbstractType{
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
            'label' => 'user.field.role',
            //it is not possible to create/edit administrators
            'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->where('r.role != :roleAdmin')
                        ->setParameter('roleAdmin', Role::$roleAdmin);
                },
        ));
        $builder->add('username', 'text', array(
            'label' => 'user.field.username',
            'attr'=>array('title' => 'form.field.title.username'),
            'pattern' => '^[a-zA-Z0-9._-]{3,20}$',
            'constraints' => array(
                new NotBlank(array('message'=>'user.name.not_blank'))
            ),
        ));
        $builder->add('email', 'email', array(
            'label' => 'user.field.email',
            'constraints' => array(
                new NotBlank(array('message'=>'user.email.not_blank'))
            ),
        ));
        $builder->add('password', 'password', array(
            'label' => 'user.field.new_password',
            'required' => false,
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
            'data_class' => 'Tixi\ApiBundle\Interfaces\Management\UserEditDTO'
        ));
    }
}