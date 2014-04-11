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
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;

class ServicePlanType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');

        $builder->add('startDate', 'dateTimePicker', array(
            'label' => 'serviceplan.field.startdate',
            'pattern' => '^(0[1-9]|[1|2][0-9]|3[0|1]).(0[1-9]|1[0|1|2]).(19|20)\d\d$',
            'constraints' => array(
                new DateTime(),
                new NotBlank(array('message'=>'serviceplan.date.not_blank'))
            ),
        ));
        $builder->add('endDate', 'dateTimePicker', array(
            'label' => 'serviceplan.field.enddate',
            'pattern' => '^(0[1-9]|[1|2][0-9]|3[0|1]).(0[1-9]|1[0|1|2]).(19|20)\d\d$',
            'constraints' => array(
                new DateTime(),
                new NotBlank(array('message'=>'serviceplan.date.not_blank'))
            ),
        ));
        $builder->add('memo', 'text', array(
            'required'  => false,
            'label' => 'serviceplan.field.memo'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'serviceplan';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\ServicePlanRegisterDTO'
        ));
    }
}