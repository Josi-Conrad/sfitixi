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

/**
 * Class ShiftSelectionType
 * @package Tixi\ApiBundle\Form\Dispo
 */
class ShiftSelectionType extends AbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('selectionId','hidden', array(
            'attr' => array('class'=>'selectionId')
        ));
        $builder->add('shiftSelection','entity',array(
            'class' => 'Tixi\CoreDomain\Dispo\ShiftType',
            'property' => 'name',
            'expanded' => true,
            'multiple' => true,
            'label' => '__label__',
            'required' => false,
            'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.isDeleted = 0');
                }
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'shiftSelection';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Dispo\ShiftSelectionDTO'
        ));
    }
}