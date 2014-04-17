<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 16.04.14
 * Time: 14:20
 */

namespace Tixi\ApiBundle\Form\Management;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;

class VehicleCategoryType extends CommonAbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array(
            'label' => 'vehicle.field.name',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'vehicle.name.not_blank'))
            ),
        ));
        $builder->add('amountOfSeats', 'integer', array(
            'label' => 'vehicle.field.category.amountofseats',
            'attr'=>array('title' => 'form.field.title.digit'),
            'constraints' => array(
                new Regex(array('message'=>'form.field.title.digit','pattern'=>'/\d+/'))
            ),
        ));
        $builder->add('amountOfWheelChairs', 'integer', array(
            'required' => false,
            'label' => 'vehicle.field.category.amountofwheelchairs',
            'attr'=>array('title' => 'form.field.title.digit'),
            'constraints' => array(
                new Regex(array('message'=>'form.field.title.digit','pattern'=>'/\d+/'))
            ),
        ));

    }
} 