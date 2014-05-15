<?php

namespace Tixi\SecurityBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Tixi\CoreDomain\Shared\CommonBaseEntity;

/**
 * @ORM\Entity(repositoryClass="Tixi\SecurityBundle\Repository\UserRepositoryDoctrine")
 * @ORM\Table(name="user")
 */
class User extends CommonBaseEntity implements AdvancedUserInterface, \Serializable {
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
        parent::__construct();
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
     * @param null $email
     * @internal param null $password
     */
    public function updateBasicData($username = null, $email = null) {
        if (!empty($username)) {
            $this->setUsername($username);
        }
        if (!empty($email)) {
            $this->setEmail($email);
        }
    }

    public function deleteLogically() {
        parent::deleteLogically();
        $this->inactivate();
    }

    public function undeleteLogically() {
        parent::undeleteLogically();
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
    public function assignRole($role) {
        if (!$this->roles->contains($role)) {
            $role->assignUser($this);
            $this->roles->add($role);
        }
    }

    /**
     * @param Role $role
     */
    public function unsignRole($role) {
        if ($this->roles->contains($role)) {
            $role->unsignUser($this);
            $this->roles->removeElement($role);
        }
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

    /**
     * @return ArrayCollection
     */
    public function getRolesEntity() {
        return $this->roles;
    }

    /**
     *
     */
    public function getHighestRole() {
        /**@var Role $role */
        $highest = null;
        $roleAdmin = null;
        $roleManager = null;
        $roleDispo = null;

        foreach ($this->getRolesEntity() as $role) {
            if ($role->getRole() == Role::$roleAdmin) {
                $roleAdmin = $role;
            }
            if ($role->getRole() == Role::$roleManager) {
                $roleManager = $role;
            }
            if ($role->getRole() == Role::$roleDispo) {
                $roleDispo = $role;
            }
        }

        if (null !== $roleDispo) {
            $highest = $roleDispo;
        }
        if (null !== $roleManager) {
            $highest = $roleManager;
        }
        if (null !== $roleAdmin) {
            $highest = $roleAdmin;
        }

        return $highest;
    }

    /**
     * @return mixed
     */
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

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return Boolean true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired() {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return Boolean true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked() {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return Boolean true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired() {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return Boolean true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled() {
        return $this->isActive;
    }

    /**
     *
     */
    public function getRolesAsString() {
        return self::constructRolesString($this->getRolesEntity());
    }

    /**
     * @param $roles
     * @return string
     */
    public static function constructRolesString($roles) {
        $string = '';
        foreach ($roles as $key => $role) {
            if ($key !== 0) {
                $string .= ', ';
            }
            $string .= $role->getRole();
        }
        return $string;
    }
}