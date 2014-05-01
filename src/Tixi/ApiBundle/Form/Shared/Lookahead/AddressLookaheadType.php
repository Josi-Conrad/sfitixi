<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 29.04.14
 * Time: 09:39
 */

namespace Tixi\ApiBundle\Form\Shared\Lookahead;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Tixi\ApiBundle\Form\Shared\AddressHandleType;

class AddressLookaheadType extends AbstractLookaheadType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('addressSelectionId','hidden');
        $builder->add('addressDisplayName','text');
        $builder->add('addressHandles', 'collection', array(
            'attr' => array('class'=>'addressHandle'),
            'type' => new AddressHandleType(),
            'allow_add' => true,
            'allow_delete' => true
        ));
        $builder->setAttribute('dataSrc',$this->generateUrl('tixiapp_service_address'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Tixi\ApiBundle\Interfaces\AddressLookaheadDTO'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'addresslookahead';
    }

    protected function getDataSrc()
    {
        return $this->generateUrl('tixiapp_service_address');
    }
}