<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 29.03.14
 * Time: 18:40
 */

namespace Tixi\CoreDomain\Dispo;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tixi\CoreDomain\Dispo\Route
 *
 * @ORM\Entity(repositoryClass="Tixi\CoreDomainBundle\Repository\Dispo\RouteRepositoryDoctrine")
 * @ORM\Table(name="route")
 */
class Route {
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    protected $startAddress;
    protected $targetAddress;
    /**
     * route duration in minutes
     * @var
     */
    protected $duration;
    /**
     * distance in m
     * @var
     */
    protected $distance;
}