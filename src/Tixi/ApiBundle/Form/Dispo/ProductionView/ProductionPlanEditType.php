<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 11:41
 */

namespace Tixi\ApiBundle\Form\Dispo\ProductionView;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;
use Tixi\ApiBundle\Interfaces\Dispo\ProductionView\ProductionPlanEditDTO;

class ProductionPlanEditType extends CommonAbstractType{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('workingMonthId', 'hidden', array(
            'attr' => array('class' => 'workingMonthId')
        ));

        $builder->add('memo', 'text', array(
            'required' => false,
            'label' => 'productionplan.field.memo',
        ));

        $builder->add('workingDays', 'collection', array(
            'type' => new ProductionViewWorkingDayType(),
            'label' => false,
        ));
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var ProductionPlanEditDTO $data */
        $data = $form->getData();
        $view->vars['dateString'] = $data->dateString;
        $view->vars['workingShiftsDisplayNames'] = $data->workingShiftsDisplayNames;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\ProductionView\ProductionPlanEditDTO'
        ));
    }

} 