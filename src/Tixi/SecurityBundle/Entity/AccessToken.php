<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 15.03.14
 * Time: 12:20
 */

namespace Tixi\SecurityBundle\Entity;


use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Model\ClientInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity
 * @ORM\Table(name="access_token")
 */
class AccessToken extends BaseAccessToken {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    protected $user;

    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client) {
        $this->client = $client;
    }

    /**
     * @return ClientInterface
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user) {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser() {
        return $this->user;
    }

}