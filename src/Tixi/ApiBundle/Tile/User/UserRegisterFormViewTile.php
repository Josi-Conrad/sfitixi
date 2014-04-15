<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 02.04.14
 * Time: 12:02
 */

namespace Tixi\ApiBundle\Tile\User;

use Symfony\Component\Validator\Constraints\DateTime;
use Tixi\ApiBundle\Tile\Core\AbstractFormViewTile;
use Tixi\ApiBundle\Tile\Core\FormRowView;

class UserRegisterFormViewTile extends AbstractFormViewTile {

    public function createFormRows() {
        $this->basicFormRows[] = new FormRowView('username', 'user.field.username',$this->dto->username);
        $this->basicFormRows[] = new FormRowView('email', 'user.field.email',$this->dto->email);
    }
}