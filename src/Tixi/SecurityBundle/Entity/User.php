<?php

namespace Tixi\SecurityBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Tixi\SecurityBundle\Repository\UserRepositoryDoctrine")
 * @ORM\Table(name="user")
 */
class User implements UserInterface, \Serializable {
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
     * @ORM\JoinTable(name="user_to_role")
     */
    private $roles;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    public function __construct() {
        $this->roles = new ArrayCollection();
        //$this->salt = sha1(uniqid(null, true)); //only without bcrypt necessary
        $this->activate();
    }

    /**
     * @param $username
     * @param $password
     * @param null $email
     * @return User
     */
    public static function registerUser($username, $password, $email = null) {
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($password);
        if (!empty($email)) {
            $user->setEmail($email);
        }
        return $user;
    }

    /**
     * @param null $username
     * @param null $password
     * @param null $email
     */
    public function updateBasicData($username = null, $password = null, $email = null) {
        if (!empty($username)) {
            $this->setUsername($username);
        }
        if (!empty($password)) {
            $this->setPassword($password);
        }
        if (!empty($email)) {
            $this->setEmail($email);
        }
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
    public function assignRole($role) {
        $role->assignUser($this);
        $this->roles->add($role);
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param $roleName
     * @internal param $role
     * @return mixed
     */
    public function hasRole($roleName) {
        $found = false;
        foreach ($this->roles as $role) {
            if ($role->getRole() == $roleName) {
                $found = true;
            }
        }
        return $found;
    }

    /**
     * @return ArrayCollection
     */
    public function getRoles() {
        return $this->roles->toArray();
    }

    public function getRolesEntity() {
        return $this->roles;
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
    }

    /**
     * @inheritDoc
     * BCrypt uses its own salt, but for that we have to return null as salt
     */
    public function getSalt() {
        return null;
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
                $this->password
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
            $this->password
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