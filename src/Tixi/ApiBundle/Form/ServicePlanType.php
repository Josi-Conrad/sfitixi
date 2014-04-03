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
use Tixi\ApiBundle\Form\Shared\DatePickerType;

class ServicePlanType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('startDate', new DatePickerType(), array(
            'label' => 'Servicestart'
        ));
        $builder->add('endDate', new DatePickerType(), array(
            'label' => 'Servicenede'
        ));
        $builder->add('cost', 'number', array(
            'label' => 'Kosten'
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
            'data_class' => 'Tixi\ApiBundle\Interfaces\ServicePlanAssignDTO'
        ));
    }
}