<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 22:26
 */

namespace Tixi\ApiBundle\Form\Dispo\MonthlyView;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanDriversPerShiftDTO;

/**
 * Class MonthlyPlanDriversPerShiftType
 * @package Tixi\ApiBundle\Form\Dispo\MonthlyView
 */
class MonthlyPlanDriversPerShiftType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('newDrivers', 'collection', array(
            'type' => new MonthlyPlanDrivingAssertionType(),
            'label' => false,
            'required' => false,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'monthlyPlanDriversPerShift';
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var MonthlyPlanDriversPerShiftDTO $data */
        $data = $form->getData();
        $view->vars['assertedDrivers'] = $data->driversWithAssertion;
        $view->vars['shiftDisplayName'] = $data->shiftDisplayName;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanDriversPerShiftDTO'
        ));
    }
}