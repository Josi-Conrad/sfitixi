<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 27.02.14
 * Time: 23:05
 */

namespace Tixi\ApiBundle\Form;


use Doctrine\ORM\UnexpectedResultException;
use phpDocumentor\Parser\Exception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Tixi\ApiBundle\Form\Shared\CommonAbstractType;

/**
 * Class AbsentType
 * @package Tixi\ApiBundle\Form
 */
class AbsentType extends CommonAbstractType{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', 'hidden');
        $builder->add('subject', 'text', array(
            'label' => 'absent.field.subject'
        ));
        $builder->add('startDate', 'datePicker', array(
            'label' => 'absent.field.startdate',
        ));
        $builder->add('endDate', 'datePicker', array(
            'label' => 'absent.field.enddate',
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\AbsentRegisterDTO',
        ));
    }
}