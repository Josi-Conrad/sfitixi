<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 05.04.14
 * Time: 09:41
 */

namespace Tixi\ApiBundle\Form\Dispo;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tixi\ApiBundle\Form\Shared\TextOnlyType;

/**
 * Class WorkingDayType
 * @package Tixi\ApiBundle\Form\Dispo
 */
class WorkingDayType extends AbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('workingDayDateString', 'textOnly');

        $builder->add('workingDayWeekDayString', 'textOnlyTranslated');

        $builder->add('workingShifts', 'collection', array(
            'type' => new WorkingShiftType(),
            'label' => false,
            'allow_add' => true,
            'allow_delete' => true,
        ));

        $builder->add('workingDayComment', 'text', array(
            'label' => false,
            'required' => false,
        ));

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'workingDay';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\WorkingDayDTO'
        ));
    }
}