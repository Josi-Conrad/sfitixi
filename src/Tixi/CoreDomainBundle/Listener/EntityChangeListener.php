<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 18.04.14
 * Time: 15:47
 */

namespace Tixi\CoreDomainBundle\Listener;

use Monolog\Logger;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Class EntityChangeListener
 * Listens to DBAL Updates and logs changes
 * @package Tixi\CoreDomainBundle\Listener
 */
class EntityChangeListener {

    /** @var \Symfony\Bridge\Monolog\Logger  */
    protected $logger;
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface  */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->logger = $this->container->get('monolog.logger.entity_channel');
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args) {
        $entity = $args->getObject();
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $this->logEntityChange('updated', $meta, $entity);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args) {
        $entity = $args->getObject();
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $this->logEntityChange('persisted', $meta, $entity);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args) {
        $entity = $args->getObject();
        $em = $args->getEntityManager();
        $meta = $em->getClassMetadata(get_class($entity));
        $this->logEntityChange('removed', $meta, $entity);
    }

    /**
     * @param $action
     * @param \Doctrine\ORM\Mapping\ClassMetadata $meta
     * @param $entity
     */
    private function logEntityChange($action, \Doctrine\ORM\Mapping\ClassMetadata $meta, $entity){
        $this->logger->info('Entity "'
            . $meta->getTableName() . '" with id: '
            . $meta->getFieldValue($entity, $meta->getSingleIdentifierFieldName()). ' '
            . $action .' by: '
            . $this->container->get('security.context')->getToken()->getUsername()
        );
    }
}