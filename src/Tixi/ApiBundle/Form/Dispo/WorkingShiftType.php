<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 09:41
 */

namespace Tixi\ApiBundle\Form\Dispo;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Tixi\ApiBundle\Interfaces\Dispo\WorkingShiftDTO;

/**
 * Class WorkingShiftType
 * @package Tixi\ApiBundle\Form\Dispo
 */
class WorkingShiftType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('workingShiftAmountOfDrivers', 'integer', array(
            'label' => false,
            'required' => true,
            'pattern' => '\d+',
            'constraints' => array(
                new Regex(array('message' => 'form.field.title.digit', 'pattern' => '/\d+/')),
            ),
        ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'workingShift';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\WorkingShiftDTO'
        ));
    }
}