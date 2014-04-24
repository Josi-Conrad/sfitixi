<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */

namespace Tixi\ApiBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Tixi\SecurityBundle\Entity\User;

/**
 * Class DriverType
 * @package Tixi\ApiBundle\Form
 */
class DriverType extends PersonType {
    /**
     * inject current User to check security roles
     * @var User $user
     */
    protected $user;

    /**
     * @param $menuId
     * @param User $user
     */
    public function __construct($menuId, User $user) {
        parent::__construct($menuId);
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        parent::buildForm($builder,$options);

        $builder->add('email', 'email', array(
            'required' => false,
            'label' => 'person.field.email'
        ));
        $builder->add('licenceNumber', 'text', array(
            'label' => 'driver.field.licence',
            'attr'=>array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message'=>'form.field.not_blank'))
            ),
        ));
        $builder->add('wheelChairAttendance', 'checkbox', array(
            'required' => false,
            'label' => 'driver.field.wheelchair'
        ));
        $builder->add('driverCategory', 'entity', array(
            'class' => 'Tixi\CoreDomain\DriverCategory',
            'property' => 'name',
            'label' => 'driver.field.category',
            'attr'=>array('title' => 'form.field.title.not_selected'),
            'constraints' => array(
                new NotBlank(array('message'=>'form.field.not_blank'))
            ),
            'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.isDeleted = 0');
                },
        ));
        $builder->add('entryDate', 'datePicker', array(
            'required' => false,
            'label' => 'person.field.entrydate',
        ));
        $builder->add('birthday', 'datePicker', array(
            'required' => false,
            'label' => 'person.field.birthday',
        ));
        $builder->add('extraMinutes', 'integer', array(
            'required' => false,
            'label' => 'person.field.extraminutes',
            'attr'=>array('title' => 'form.field.title.digit'),
            'constraints' => array(
                new Regex(array('message'=>'form.field.title.digit','pattern'=>'/\d+/'))
            ),
        ));

        if($this->user->hasRole('ROLE_MANAGER')){
            $builder->add('details', 'textarea', array(
                'required' => false,
                'label' => 'person.field.details'
            ));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\DriverRegisterDTO'
        ));
    }
}