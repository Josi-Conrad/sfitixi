<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 28.04.14
 * Time: 09:39
 */

namespace Tixi\ApiBundle\Form\Management;


use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;
use Tixi\CoreDomain\ZonePlanRepository;

/**
 * Class ZonePlanType
 * @package Tixi\ApiBundle\Form\Management
 */
class ZonePlanType extends CommonAbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('city', 'text', array(
            'label' => 'zoneplan.field.city',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank')),
            )));
        $builder->add('postalCode', 'text', array(
            'label' => 'zoneplan.field.postalcode',
            'pattern' => '^[\+0-9A-Z*]{4,7}',
            'attr' => array('title' => 'form.field.title.postalcode'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('zone', 'entity', array(
            'class' => 'Tixi\CoreDomain\Zone',
            'property' => 'name',
            'label' => 'zoneplan.field.zone',
            'attr' => array('title' => 'form.field.title.not_selected'),
            'constraints' => array(
                new NotBlank(array('message' => 'form.field.not_blank'))
            ),
            'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.isDeleted = 0');
                },
        ));
        $builder->add('memo', 'textarea', array(
            'label' => 'zoneplan.field.memo',
            'required' => false
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\Management\ZonePlanRegisterDTO'
        ));
    }
} 