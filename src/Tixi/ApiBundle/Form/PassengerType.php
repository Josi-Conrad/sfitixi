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

        $builder->add('isBillingAddress', 'checkbox', array(
            'required' => false,
            'label' => 'passenger.field.isbillingaddress'
        ));
        $builder->add('billingAddress', 'textarea', array(
            'required' => false,
            'label' => 'person.field.billingaddress'
        ));

        $builder->add('gotMonthlyBilling', 'checkbox', array(
            'required' => false,
            'label' => 'passenger.field.monthlybilling'
        ));
        $builder->add('preferredVehicleCategory', 'entity', array(
            'required' => false,
            'class' => 'Tixi\CoreDomain\VehicleCategory',
            'property' => 'name',
            'empty_data' => null,
            'empty_value' => 'person.field.preferredvehiclecategory.empty',
            'label' => 'person.field.preferredvehiclecategory',
            'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.isDeleted = 0');
                },
        ));
        $builder->add('isInWheelChair', 'checkbox', array(
            'required' => false,
            'label' => 'passenger.field.isinwheelchair'
        ));
        $builder->add('handicaps', 'entity', array(
            'required' => false,
            'class' => 'Tixi\CoreDomain\Handicap',
            'property' => 'name',
            'expanded' => true,
            'multiple' => true,
            'label' => 'passenger.field.handicap',
            'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.isDeleted = 0');
                },
        ));
        $builder->add('insurances', 'entity', array(
            'required' => false,
            'class' => 'Tixi\CoreDomain\Insurance',
            'property' => 'name',
            'expanded' => true,
            'multiple' => true,
            'label' => 'passenger.field.insurance',
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
            'attr' => array('title' => 'form.field.title.digit'),
            'constraints' => array(
                new Regex(array('message' => 'form.field.title.digit', 'pattern' => '/\d+/'))
            ),
        ));
        $builder->add('notice', 'textarea', array(
            'required' => false,
            'label' => 'passenger.field.notice'
        ));

        $builder->add('correspondenceAddress', 'textarea', array(
            'required' => false,
            'label' => 'person.field.correspondenceaddress'
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