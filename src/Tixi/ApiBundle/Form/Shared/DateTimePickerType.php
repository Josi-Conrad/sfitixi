<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 11.04.14
 * Time: 10:56
 */

namespace Tixi\ApiBundle\Form\Shared;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Time;

/**
 * Class DateTimePickerType
 * @package Tixi\ApiBundle\Form\Shared
 */
class DateTimePickerType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('date', 'datePicker', array(
                'input' => 'datetime',
            ))
            ->add('time', 'time', array(
                'input' => 'datetime',
                'widget' => 'single_text',
                'attr' => array(
                    'title' => 'form.field.title.datetime',
                ),
                'pattern' => '^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$',
                    'constraints' => array(
                        new Time(),
                    ),
            ));

        $builder->addModelTransformer(new DateTimeArrayTransformer());
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array());
    }

    /**
     * {@inheritdoc}
     */
    public function getParent() {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'dateTimePicker';
    }

} 