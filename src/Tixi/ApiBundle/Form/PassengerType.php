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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Regex;
use Tixi\ApiBundle\Form\Shared\DatePickerType;

class PassengerType extends PersonType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        parent::buildForm($builder,$options);

        $builder->add('handicaps', 'entity', array(
            'class'     => 'Tixi\CoreDomain\Handicap',
            'property'  =>  'name',
            'expanded'  => true,
            'multiple'  => true,
            'label'     => 'passenger.field.handicap'
        ));
        $builder->add('insurances', 'entity', array(
            'class'     => 'Tixi\CoreDomain\Insurance',
            'property'  =>  'name',
            'expanded'  => true,
            'multiple'  => true,
            'label'     => 'passenger.field.insurance'
        ));
        $builder->add('gotMonthlyBilling', 'checkbox', array(
            'required'  => false,
            'label' => 'passenger.field.monthlybilling'
        ));
        $builder->add('entryDate', new DatePickerType(), array(
            'required' => false,
            'label' => 'person.field.entrydate',
            'attr'=>array('title' => 'form.field.title.date'),
            'pattern' => '^(0[1-9]|[1|2][0-9]|3[0|1]).(0[1-9]|1[0|1|2]).(19|20)\d\d$',
            'constraints' => array(
                new DateTime(),
            ),
        ));
        $builder->add('birthday', new DatePickerType(), array(
            'required' => false,
            'label' => 'person.field.birthday',
            'attr'=>array('title' => 'form.field.title.date'),
            'pattern' => '^(0[1-9]|[1|2][0-9]|3[0|1]).(0[1-9]|1[0|1|2]).(19|20)\d\d$',
            'constraints' => array(
                new DateTime(),
            ),
        ));
        $builder->add('extraMinutes', 'integer', array(
            'required' => false,
            'label' => 'person.field.extraminutes',
            'attr'=>array('title' => 'form.field.title.digit'),
            'constraints' => array(
                new Regex(array('message'=>'form.field.title.digit','pattern'=>'/\d+/'))
            ),
        ));
        $builder->add('notice', 'textarea', array(
            'required'  => false,
            'label' => 'passenger.field.notice'
        ));
        $builder->add('details', 'textarea', array(
            'required' => false,
            'label' => 'person.field.details'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'passenger';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\PassengerRegisterDTO'
        ));
    }
}