<?php
/**
 * Created by PhpStorm.
 * User: hert
 * Date: 23.03.14
 * Time: 20:31
 */

namespace Tixi\ApiBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tixi\ApiBundle\Form\Shared\TelephoneType;

class POIType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {

        parent::buildForm($builder, $options);

        $builder->add('name', 'text', array(
            'label' => 'poi.field.name',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('department', 'text', array(
            'required' => false,
            'label' => 'poi.field.department',
        ));
        $builder->add('telephone', new TelephoneType(), array(
            'required' => false,
            'label' => 'poi.field.telephone',
        ));
        $builder->add('keywords', 'entity', array(
            'required' => false,
            'class' => 'Tixi\CoreDomain\POIKeyword',
            'property' => 'name',
            'label' => 'poi.field.keyword',
            'multiple' => true,
            'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('k')
                        ->orderBy('k.name', 'ASC');
                },
        ));
        $builder->add('street', 'text', array(
            'label' => 'address.field.street',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('postalCode', 'text', array(
            'label' => 'address.field.postalcode',
            'pattern' => '^[\+0-9A-Z]{4,7}',
            'attr' => array('title' => 'form.field.title.postalcode'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('city', 'text', array(
            'label' => 'address.field.city',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('country', 'text', array(
            'label' => 'address.field.country',
            'attr' => array('title' => 'form.field.title.not_blank'),
            'constraints' => array(
                new NotBlank(array('message' => 'field.not_blank'))
            ),
        ));
        $builder->add('lat', 'text', array(
            'required' => false,
            'label' => 'address.field.lat'
        ));
        $builder->add('lng', 'text', array(
            'required' => false,
            'label' => 'address.field.lng'
        ));

        $builder->add('memo', 'textarea', array(
            'required' => false,
            'label' => 'poi.field.memo',
        ));
        $builder->add('comment', 'textarea', array(
            'required' => false,
            'label' => 'poi.field.comment',
        ));
        $builder->add('details', 'textarea', array(
            'required' => false,
            'label' => 'poi.field.details',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'poi';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\POIRegisterDTO'
        ));
    }
}