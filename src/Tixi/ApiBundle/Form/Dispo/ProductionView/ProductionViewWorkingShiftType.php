<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 26.05.14
 * Time: 14:37
 */

namespace Tixi\ApiBundle\Form\Dispo\ProductionView;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Regex;

class ProductionViewWorkingShiftType extends AbstractType{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('amountOfDrivers', 'integer', array(
            'label' => false,
            'required' => true,
            'pattern' => '\d+',
            'attr' => array(
                'min' => 0,
                'title' => 'form.field.title.digitpositive'
            ),
            'constraints' => array(
                new Regex(array('message' => 'form.field.title.digitpositive', 'pattern' => '/^[0-9]\d*$/')),
            ),
        ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "productionViewWorkingShift";
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\ProductionView\ProductionViewWorkingShiftDTO'
        ));
    }
}