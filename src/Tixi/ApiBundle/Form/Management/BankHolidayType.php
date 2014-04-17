<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.02.14
 * Time: 23:05
 */

namespace Tixi\ApiBundle\Form\Management;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;

/**
 * Class BankHolidayType
 * @package Tixi\ApiBundle\Form
 */
class BankHolidayType extends CommonAbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array(
            'label' => 'bankholiday.field.name'
        ));
        $builder->add('startDate', 'datePicker', array(
            'label' => 'bankholiday.field.start',
        ));
        $builder->add('endDate', 'datePicker', array(
            'label' => 'bankholiday.field.end',
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Management\BankHolidayRegisterDTO'
        ));
    }
}