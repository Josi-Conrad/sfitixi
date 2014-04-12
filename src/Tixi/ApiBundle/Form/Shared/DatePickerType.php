<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 23.03.14
 * Time: 20:45
 */

namespace Tixi\ApiBundle\Form\Shared;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class DatePickerType
 * @package Tixi\ApiBundle\Form\Shared
 */
class DatePickerType extends AbstractType {
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'format' => 'dd.MM.yyyy',
            'attr' => array(
                'title' => 'form.field.title.date',
            ),
            'pattern' => '^(0[1-9]|[1|2][0-9]|3[0|1]).(0[1-9]|1[0|1|2]).(19|20)\d\d$',
            'constraints' => array(
                new DateTime(),
            ),
        ));
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent() {
        return 'date';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName() {
        return 'datePicker';
    }
}