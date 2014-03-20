<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 06.03.14
 * Time: 21:09
 */

namespace Tixi\ApiBundle\Interfaces;


class PassengerDTO {

    public $id;
    public $title;

    /**
     * @Assert\NotBlank(message = "vehicle.name.not_blank")
     */
    public $firstname;

    /**
     * @Assert\NotBlank(message = "vehicle.name.not_blank")
     */
    public $lastname;
    public $telephone;
    public $isInWheelChair;

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getFirstname() {
        return $this->firstname;
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
     * @param mixed $isInWheelChair
     */
    public function setIsInWheelChair($isInWheelChair) {
        $this->isInWheelChair = $isInWheelChair;
    }

    /**
     * @return mixed
     */
    public function getIsInWheelChair() {
        return $this->isInWheelChair;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getTelephone() {
        return $this->telephone;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle() {
        return $this->title;
    }


}