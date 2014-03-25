<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.03.14
 * Time: 20:45
 */

namespace Tixi\ApiBundle\Form\Shared;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatePickerType extends AbstractType{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'format' => 'dd.MM.yyyy'
        ));
    }

    public function getParent()
    {
        return 'date';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'datepicker';
    }
}