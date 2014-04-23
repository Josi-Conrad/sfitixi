<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */

namespace Tixi\ApiBundle\Form;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Regex;
use Tixi\SecurityBundle\Entity\User;

/**
 * Class PassengerType
 * @package Tixi\ApiBundle\Form
 */
class PassengerType extends PersonType {
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

        parent::buildForm($builder, $options);

        $builder->add('handicaps', 'entity', array(
            'class' => 'Tixi\CoreDomain\Handicap',
            'property' => 'name',
            'expanded' => true,
            'multiple' => true,
            'label' => 'passenger.field.handicap'
        ));
        $builder->add('insurances', 'entity', array(
            'class' => 'Tixi\CoreDomain\Insurance',
            'property' => 'name',
            'expanded' => true,
            'multiple' => true,
            'label' => 'passenger.field.insurance'
        ));
        $builder->add('gotMonthlyBilling', 'checkbox', array(
            'required' => false,
            'label' => 'passenger.field.monthlybilling'
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
            'attr' => array('title' => 'form.field.title.digit'),
            'constraints' => array(
                new Regex(array('message' => 'form.field.title.digit', 'pattern' => '/\d+/'))
            ),
        ));
        $builder->add('notice', 'textarea', array(
            'required' => false,
            'label' => 'passenger.field.notice'
        ));

        if ($this->user->hasRole('ROLE_MANAGER')) {
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
            'data_class' => 'Tixi\ApiBundle\Interfaces\PassengerRegisterDTO',
        ));
    }
}