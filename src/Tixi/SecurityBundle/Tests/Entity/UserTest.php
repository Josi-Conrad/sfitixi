<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 14.03.14
 * Time: 17:31
 */

namespace Tixi\SecurityBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Tixi\SecurityBundle\Entity\Role;
use Tixi\SecurityBundle\Entity\User;

class UserTest extends WebTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Tixi\SecurityBundle\Repository\UserRepositoryDoctrine
     */
    private $userRepo;
    /**
     * @var \Tixi\SecurityBundle\Repository\RoleRepositoryDoctrine
     */
    private $roleRepo;
    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
     */
    private $encFactory;

    public function setUp() {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->em = $kernel->getContainer()->get('entity_manager');
        $this->userRepo = $kernel->getContainer()->get('tixi_user_repository');
        $this->roleRepo = $kernel->getContainer()->get('tixi_role_repository');
        $this->encFactory = $kernel->getContainer()->get('security.encoder_factory');
        $this->em->beginTransaction();
    }

    public function testUserCRUD() {
        $role_user = $this->createRole('Benutzer', 'ROLE_USER');
        $role_manager = $this->createRole('Manager', 'ROLE_MANAGER');
        $role_admin = $this->createRole('Admin', 'ROLE_ADMIN');
        $user1 = $this->createUser('admin', 'pass', array($role_user, $role_manager, $role_admin));
        $user2 = $this->createUser('manager', 'pass', array($role_user, $role_manager));
        $user3 = $this->createUser('user', 'pass', array($role_user));
        $this->em->flush();
        $user_find = $this->userRepo->find($user1->getId());
        $this->assertEquals($user1, $user_find);
        $user_find = $this->userRepo->find($user2->getId());
        $this->assertEquals($user2, $user_find);
        $user_find = $this->userRepo->find($user3->getId());
        $this->assertEquals($user3, $user_find);

    }

    /**
     * @param $name
     * @param $roleName
     * @return null|object|Role
     */
    public function createRole($name, $roleName) {
        $role = $this->roleRepo->findOneBy(array('role' => $roleName));
        if (empty($role)) {
            $role = Role::registerRole($name, $roleName);
            $this->roleRepo->store($role);
        }
        return $role;
    }

    /**
     * @param $username
     * @param $password
     * @param $roles
     * @return User
     */
    public function createUser($username, $password, $roles) {
        $user = $this->userRepo->findOneBy(array('username' => $username));
        if(empty($user)){
            $user = new User();
            $user->setUsername($username);

            $encoder = $this->encFactory->getEncoder($user);
            $encPassword = $encoder->encodePassword($password, $user->getSalt());
            if (!$encoder->isPasswordValid($encPassword, $password, $user->getSalt())) {
                $this->assert('Password not valid');
            } else {
                $user->setPassword($encPassword);
            }

            foreach($roles as $role){
                $user->assignRole($role);
            }

            $this->userRepo->store($user);
        }
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->rollback();
    }
}