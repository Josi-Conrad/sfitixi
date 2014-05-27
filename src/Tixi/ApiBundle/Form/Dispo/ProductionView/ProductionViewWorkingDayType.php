<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 11:40
 */

namespace Tixi\ApiBundle\Form\Dispo\ProductionView;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tixi\ApiBundle\Interfaces\Dispo\ProductionView\ProductionViewWorkingDayDTO;

class ProductionViewWorkingDayType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('workingShifts', 'collection', array(
            'type' => new ProductionViewWorkingShiftType(),
            'label' => false,
        ));

        $builder->add('comment', 'text', array(
            'label' => false,
            'required' => false,
        ));

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var ProductionViewWorkingDayDTO $data */
        $data = $form->getData();
        $view->vars['dateString'] = $data->dateString;
        $view->vars['weekDayString'] = $data->weekDayString;
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'productionViewWorkingDay';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\ProductionView\ProductionViewWorkingDayDTO'
        ));
    }
}