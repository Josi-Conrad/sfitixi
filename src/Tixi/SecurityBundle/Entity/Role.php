<?php

namespace Tixi\SecurityBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Tixi\SecurityBundle\Repository\RoleRepositoryDoctrine")
 * @ORM\Table(name="role")
 */
class Role implements RoleInterface, \Serializable {
    /**
     * All valid Roles, with highest Role at last place
     * @var string
     */
    public static $roleDispo = 'ROLE_DISPO';
    public static $roleDispoName = 'user.role.dispo';
    public static $roleManager = 'ROLE_MANAGER';
    public static $roleManagerName = 'user.role.manager';
    public static $roleAdmin = 'ROLE_ADMIN';
    public static $roleAdminName = 'user.role.admin';

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

    private function __construct() {
        $this->users = new ArrayCollection();
    }

    /**
     * @param $name
     * @param $roleName
     * @return Role
     */
    public static function registerRole($name, $roleName) {
        $role = new Role();
        $role->setName($name);
        $role->setRole($roleName);
        return $role;
    }

    /**
     * @param $user
     */
    public function assignUser($user) {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    /**
     * @param $user
     */
    public function unsignUser($user) {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }
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