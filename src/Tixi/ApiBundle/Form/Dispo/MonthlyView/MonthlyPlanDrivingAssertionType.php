<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.05.14
 * Time: 22:00
 */

namespace Tixi\ApiBundle\Form\Dispo\MonthlyView;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MonthlyPlanDrivingAssertionType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('driver', 'entity', array(
            'class' => 'Tixi\CoreDomain\Driver',
            'property' => 'nameStringWithID',
            'required' => false,
            'empty_data' => null,
            'empty_value' => 'monthlyplan.edit.driverselection.field.empty',
            'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->where('s.isDeleted = 0')
                        ->orderBy('s.firstname', 'ASC');
                },
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'monthlyPlanDrivingAssertion';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\MonthlyView\MonthlyPlanDrivingAssertionDTO'
        ));
    }

}