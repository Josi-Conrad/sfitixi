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
        $builder->add('firstname', 'text', array(
            'label' => 'person.field.firstname',
        ));
        $builder->add('lastname', 'text', array(
            'label' => 'person.field.lastname',
        ));
        $builder->add('telephone', 'text', array(
            'label' => 'person.field.telephone',
            'pattern' => '^[\+0-9 ]{5,19}'
        ));
        $builder->add('street', 'text', array(
            'label' => 'address.field.street'
        ));
        $builder->add('postalCode', 'text', array(
            'label' => 'address.field.postalcode'
        ));
        $builder->add('city', 'text', array(
            'label' => 'address.field.city'
        ));
        $builder->add('country', 'text', array(
            'label' => 'address.field.country'
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