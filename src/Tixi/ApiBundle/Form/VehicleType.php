<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.02.14
 * Time: 23:05
 */

namespace Tixi\ApiBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;
use Tixi\SecurityBundle\Entity\User;

/**
 * Class VehicleType
 * @package Tixi\ApiBundle\Form
 */
class VehicleType extends CommonAbstractType {
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
        $builder->add('id', 'hidden');
        $builder->add('category', 'entity', array(
            'class' => 'Tixi\CoreDomain\VehicleCategory',
            'property' => 'name',
            'label' => 'vehicle.field.category'
        ));
        $builder->add('name', 'text', array(
            'label' => 'vehicle.field.name',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'vehicle.name.not_blank'))
            ),
        ));
        $builder->add('licenceNumber', 'text', array(
            'label' => 'vehicle.field.licencenumber',
            'attr' => array('title' => 'form.field.title.gletter_digit_space'),
            'pattern' => '^[A-Z\d ]+$',
            'constraints' => array(
                new NotBlank(array('message' => 'vehicle.nr.not_blank'))
            ),
        ));
        $builder->add('dateOfFirstRegistration', 'datePicker', array(
            'label' => 'vehicle.field.dateoffirstregistration',
        ));
        $builder->add('supervisor', 'entity', array(
            'class' => 'Tixi\CoreDomain\Driver',
            'property' => 'nameStringWithID',
            'label' => 'vehicle.field.supervisor',
            'required' => false,
            'empty_data' => null,
            'empty_value' => 'vehicle.field.supervisor.empty',
            'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.firstname', 'ASC');
                },
        ));
        $builder->add('parking', 'text', array(
            'required' => false,
            'label' => 'vehicle.field.parking'
        ));
        $builder->add('memo', 'textarea', array(
            'label' => 'vehicle.field.memo',
            'required' => false,
        ));

        if ($this->user->hasRole('ROLE_MANAGER')) {
            $builder->add('managementDetails', 'textarea', array(
                'label' => 'vehicle.field.managementdetails',
                'required' => false,
            ));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\VehicleRegisterDTO'
        ));
    }
}