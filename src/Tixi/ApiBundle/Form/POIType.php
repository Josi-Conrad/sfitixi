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
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;
use Tixi\ApiBundle\Form\Shared\TelephoneType;

/**
 * Class POIType
 * @package Tixi\ApiBundle\Form
 */
class POIType extends CommonAbstractType {
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
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
        $builder->add('telephone', 'telephoneType', array(
            'required' => false,
            'label' => 'poi.field.telephone',
        ));
        $builder->add('keywords', 'entity', array(
            'required' => false,
            'class' => 'Tixi\CoreDomain\POIKeyword',
            'property' => 'name',
            'label' => 'poi.field.keyword',
            'multiple' => true,
            'expanded' => true,
            'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('k')
                        ->where('k.isDeleted = 0')
                        ->orderBy('k.name', 'ASC');
                },
        ));
        $builder->add('address','addresslookahead',array(
                'label' => 'address.field.lookahead'
            )
        );

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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\POIRegisterDTO'
        ));
    }
}