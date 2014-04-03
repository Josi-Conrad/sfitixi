<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 03.04.14
 * Time: 19:00
 */

namespace Tixi\ApiBundle\Form\Shared;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TextOnlyType extends AbstractType {
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'compound' => false,
            'pattern' => '^([ \u00c0-\u01ffa-zA-Z\'\-\.])+$',
        ));
    }

    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'textOnly';
    }
}
