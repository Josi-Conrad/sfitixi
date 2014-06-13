<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 11:42
 */

namespace Tixi\ApiBundle\Form\Dispo\ProductionView;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;

/**
 * Class ProductionPlanCreateType
 * @package Tixi\ApiBundle\Form\Dispo\ProductionView
 */
class ProductionPlanCreateType extends CommonAbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $yearRange = range(date('Y'), date('Y') + 5);
        $builder->add('year', 'choice', array(
                'label' => 'productionplan.field.year',
                'choices' => array_combine($yearRange, $yearRange),
            )
        );

        $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        $builder->add('month', 'choice', array(
                'label' => 'productionplan.field.month',
                'choices' => array_combine($months, $months),
            )
        );

        $builder->add('memo', 'text', array(
            'label' => 'productionplan.field.memo',
            'required' => false,
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\ProductionView\ProductionPlanCreateDTO'
        ));
    }

} 