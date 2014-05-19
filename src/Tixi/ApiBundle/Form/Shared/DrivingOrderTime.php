<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 19.05.14
 * Time: 23:54
 */

namespace Tixi\ApiBundle\Form\Shared;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Time;

class DrivingOrderTime extends AbstractType{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('outwardTime', 'time', array(
            'required' => false,
            'label' => 'drivingorder.field.outwardTime',
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

        $builder->add('returnTime', 'time', array(
            'required' => false,
            'label' => 'drivingorder.field.returnTime',
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
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'drivingOrderTime';
    }
}