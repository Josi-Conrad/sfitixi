<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */

namespace Tixi\ApiBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tixi\ApiBundle\Form\Shared\DatePickerType;
use Tixi\ApiBundle\Form\Shared\TextOnlyType;

class PersonType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder->add('title', 'choice', array(
            'label' => 'person.field.title',
            'choices' => array(
                'm' => 'person.title.male',
                'f' => 'person.title.female'),
            'multiple' => false,
            'expanded' => true
        ));
        $builder->add('firstname', new TextOnlyType(), array(
            'label' => 'person.field.firstname',
        ));
        $builder->add('lastname', new TextOnlyType(), array(
            'label' => 'person.field.lastname',
        ));
        $builder->add('telephone', 'text', array(
            'label' => 'person.field.telephone',
            'pattern' => '^[\+0-9 ]{5,19}'
        ));
        $builder->add('email', 'email', array(
            'required' => false,
            'label' => 'person.field.email'
        ));
        $builder->add('entryDate', new DatePickerType(), array(
            'required' => false,
            'label' => 'person.field.entrydate'
        ));
        $builder->add('birthday', new DatePickerType(), array(
            'required' => false,
            'label' => 'person.field.birthday'
        ));
        $builder->add('extraMinutes', 'integer', array(
            'required' => false,
            'label' => 'person.field.extraminutes',
            'pattern' => '^\d+$'
        ));
        $builder->add('details', 'textarea', array(
            'required' => false,
            'label' => 'person.field.details'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'person';
    }
}