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
 * Class ShiftTypeType
 * @package Tixi\ApiBundle\Form
 */
class ShiftTypeType extends CommonAbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array(
            'label' => 'shifttype.field.name'
        ));
        $builder->add('start', 'time', array(
            'label' => 'shifttype.field.start',
            'widget' => 'single_text',
            'error_bubbling' => true
        ));
        $builder->add('end', 'time', array(
            'label' => 'shifttype.field.end',
            'widget' => 'single_text',
            'error_bubbling' => true
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Management\ShiftTypeRegisterDTO'
        ));
    }
}