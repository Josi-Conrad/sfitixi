<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 22:11
 */

namespace Tixi\ApiBundle\Form\Dispo\MonthlyView;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;
use Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanEditDTO;

/**
 * Class MonthlyPlanEditType
 * @package Tixi\ApiBundle\Form\Dispo\MonthlyView
 */
class MonthlyPlanEditType extends CommonAbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('shifts', 'collection', array(
            'type' => new MonthlyPlanDriversPerShiftType(),
            'label' => false,
            'required' => false,
        ));
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var MonthlyPlanEditDTO $data */
        $data = $form->getData();
        $view->vars['workingMonthDateString'] = $data->workingMonthDateString;
        $view->vars['workingDayWeekdayString'] = $data->workingDayWeekdayString;
        $view->vars['workingDayDateString'] = $data->workingDayDateString;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanEditDTO'
        ));
    }


} 