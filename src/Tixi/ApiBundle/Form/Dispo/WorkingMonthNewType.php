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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;
use Tixi\ApiBundle\Interfaces\Dispo\WorkingMonthNewDTO;

/**
 * Class WorkingMonthNewType
 * @package Tixi\ApiBundle\Form\Dispo
 */
class WorkingMonthNewType extends CommonAbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('workingMonthDateYear', 'choice', array(
                'label' => 'workingmonth.field.year',
                'choices' => range(date('Y'), date('Y') + 10)
            )
        );

        $builder->add('workingMonthDateMonth', 'choice', array(
                'label' => 'workingmonth.field.month',
                'choices' => array('01','02','03','04','05','06','07','08','09','10','11','12'),
            )
        );

        $builder->add('workingMonthMemo', 'text', array(
            'label' => 'workingmonth.field.memo',
            'required' => false,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'workingMonthNew';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\WorkingMonthNewDTO'
        ));
    }
}