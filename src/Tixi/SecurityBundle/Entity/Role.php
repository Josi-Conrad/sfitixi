<?php

namespace Tixi\SecurityBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Tixi\SecurityBundle\Repository\RoleRepositoryDoctrine")
 * @ORM\Table(name="role")
 */
class Role implements RoleInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
     */
    private $users;

    /**
     * @ORM\Column(name="name", type="string", length=25)
     */
    private $name;

    /**
     * @ORM\Column(name="role", type="string", length=25, unique=true)
     */
    private $role;

    private function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public static function registerRole($name, $roleName){
        $role = new Role();
        $role->setName($name);
        $role->setRole($roleName);
        return $role;
    }

    public function assignUser($user){
        $this->users->add($user);
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users) {
        $this->users = $users;
    }

    /**
     * @return mixed
     */
    public function getUsers() {
        return $this->users;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role) {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getRole() {
        return $this->role;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize() {
        return serialize(
            array(
                $this->id,
                $this->role,
                $this->name
            )
        );
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        list (
            $this->id,
            $this->role,
            $this->name
            ) = unserialize($serialized);
    }
}