<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 03.06.14
 * Time: 18:54
 */

namespace Tixi\App\AppBundle\Mail;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\Mail\MailService;
use Swift_Message;
use Swift_Attachment;

class MailServiceSwiftMailer extends ContainerAware implements MailService {
    /**
     * sends a html mail to an array of recipients
     * @param $recipients
     * @param $subject
     * @param $html
     * @param null $file
     * @return bool
     */
    public function mailToSeveralRecipients($recipients, $subject, $html, $file = null) {
        $mailer = $this->container->get('mailer');
        $fromParameter = $this->container->getParameter('tixi_parameter_admin_mail');

        $message = Swift_Message::newInstance();

        $attach = null;
        if (!empty($file)) {
            $attach = Swift_Attachment::fromPath($file);
        }
        if ($attach) {
            $message->attach($attach);
        }

        $message->setSubject($subject)
            ->setFrom($fromParameter)
            ->setTo($recipients)
            ->setReadReceiptTo($fromParameter)
            ->setBody($html, 'text/html', 'utf8');

        $failures = null;
        if (!$mailer->send($message, $failures)) {
            echo "Mail send failures:";
            print_r($failures);
            return false;
        } else {
            echo "Mail send successfull";
            return true;
        }
    }
}