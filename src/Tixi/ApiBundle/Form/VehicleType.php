<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.02.14
 * Time: 23:05
 */

namespace Tixi\ApiBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Tixi\ApiBundle\Form\Shared\DatePickerType;

class VehicleType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('name', 'text', array(
            'label' => 'vehicle.field.name',
            'attr'=>array('title' => 'form.field.title.notBlank'),
            'constraints' => new NotBlank(array('message'=>'vehicle.name.not_blank')),

        ));
        $builder->add('licenceNumber', 'text', array(
            'label' => 'vehicle.field.licencenumber',
            'attr'=>array('title' => 'form.field.title.digit'),
            'constraints' => array(
                new NotBlank(array('message'=>'vehicle.nr.not_blank')),
                new Regex(array('message'=>'vehicle.nr.not_nr','pattern'=>'/\d+/'))),
            'pattern' => '^\d+$'
        ));
        $builder->add('dateOfFirstRegistration', new DatePickerType(), array(
            'label' => 'vehicle.field.dateoffirstregistration',
            'attr'=>array('title' => 'form.field.title.date'),
            'constraints' => new DateTime(),
            'pattern' => '^(0[1-9]|[1|2][0-9]|3[0|1]).(0[1-9]|1[0|1|2]).(19|20)\d\d$'
        ));
        $builder->add('parkingLotNumber', 'integer', array(
            'label' => 'vehicle.field.parkinglotnumber',
            'attr'=>array('title' => 'form.field.title.digit'),
            'constraints' => array(
                new NotBlank(array('message'=>'vehicle.nr.not_blank'))
            )
        ));
        $builder->add('supervisor', 'entity', array(
            'class' => 'Tixi\CoreDomain\Driver',
            'property' => 'nameStringWithID',
            'label' => 'vehicle.field.supervisor',
            'required'  => false,
            'empty_data' => null,
            'empty_value' => 'vehicle.field.supervisor.empty',
            'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.firstname', 'ASC');
                },
        ));
        $builder->add('category', 'entity', array(
            'class' => 'Tixi\CoreDomain\VehicleCategory',
            'property' => 'name',
            'label' => 'vehicle.field.category'
        ));
        $builder->add('memo', 'textarea', array(
            'label' => 'vehicle.field.memo',
            'required'  => false,
        ));
        $builder->add('managementDetails', 'textarea', array(
            'label' => 'vehicle.field.managementdetails',
            'required'  => false,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'vehicle';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\VehicleRegisterDTO'
        ));
    }
}