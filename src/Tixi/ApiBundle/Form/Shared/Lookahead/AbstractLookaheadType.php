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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractLookaheadType extends AbstractType implements ContainerAwareInterface{

    protected $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['dataSrc'] = $this->getDataSrc();
        parent::buildView($view, $form, $options);

    }

    abstract protected function getDataSrc();
} 