<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 10.04.14
 * Time: 08:58
 */

namespace Tixi\ApiBundle\Form\Shared;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class TelephoneType
 * @package Tixi\ApiBundle\Form\Shared
 */
class TelephoneType extends AbstractType {
    protected $pattern = '^[\+0-9 -()]{3,19}';
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
//            'pattern' => $this->pattern,
            'attr' => array('title' => 'form.field.title.telephone'),
//            'constraints' => array(
//                new Regex(array('message'=>'field.telephone','pattern'=> '/'.$this->pattern.'/'))
//            ),
        ));
    }

    /**
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent() {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'telephoneType';
    }
}