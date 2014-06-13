<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 30.04.14
 * Time: 09:58
 */

namespace Tixi\App\Mail;


/**
 * responsible and facade for an easy use of the mail service
 * Interface MailService
 * @package Tixi\App\Routing
 */
interface MailService {
    /**
     * sends a html mail to an array of recipients
     * @param $recipients
     * @param $subject
     * @param $html
     * @param null $file
     * @return bool
     */
    public function mailToSeveralRecipients($recipients, $subject, $html, $file = null);

}
