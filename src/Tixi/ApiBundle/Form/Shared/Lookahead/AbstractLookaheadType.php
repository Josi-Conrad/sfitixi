<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 29.04.14
 * Time: 15:53
 */

namespace Tixi\ApiBundle\Form\Shared\Lookahead;


use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class AbstractLookaheadType
 * @package Tixi\ApiBundle\Form\Shared\Lookahead
 */
abstract class AbstractLookaheadType extends AbstractType implements ContainerAwareInterface{
    /**
     * @var $container ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * @param $route
     * @param array $parameters
     * @param bool $referenceType
     * @return mixed
     */
    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'lookahead_id' => 'lookahead',
            'late_init' => false
        ));
    }


    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['dataSrc'] = $this->getDataSrc();
        $view->vars['lookaheadId'] = $options['lookahead_id'];
        $view->vars['lateInit'] = $options['late_init'];
        parent::buildView($view, $form, $options);

    }

    /**
     * @return mixed
     */
    abstract protected function getDataSrc();
} 