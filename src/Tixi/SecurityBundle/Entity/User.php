<?php

namespace Tixi\SecurityBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Tixi\SecurityBundle\Repository\UserRepositoryDoctrine")
 */
class User implements UserInterface, \Serializable {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salt;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="user_roles")
     */
    private $roles;

    public function __construct() {
        $this->roles = new ArrayCollection();
        $this->salt = sha1(uniqid(null, true));
        $this->activate();
    }


    public function activate() {
        $this->isActive = true;
    }

    public function inactivate() {
        $this->isActive = false;
    }

    /**
     * @param Role $role
     */
    public function assignRole($role)
    {
        $role->assignUser($this);
        $this->roles->add($role);
    }

    /**
     * @return ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function getId() {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function setUsername($username) {
        $this->username = $username;
        $this->email = $username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt() {
        return $this->salt;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize() {
        return serialize(
            array(
                $this->id,
                $this->username,
                $this->password,
                $this->salt
            )
        );
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized) {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {
        // TODO: Implement eraseCredentials() method.
    }
}