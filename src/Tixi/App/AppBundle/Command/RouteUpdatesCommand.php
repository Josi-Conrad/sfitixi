<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 29.03.14
 * Time: 17:53
 */

namespace Tixi\App\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tixi\CoreDomain\Address;

/**
 * Class RouteUpdatesCommand
 * @package Tixi\App\AppBundle\Command
 */
class RouteUpdatesCommand extends ContainerAwareCommand {
    public function configure() {
        $this->setName('project:route-update')
            ->setDescription('Updates available Routes in database after 1 month');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $em = $this->getContainer()->get('entity_manager');
        $routeMachine = $this->getContainer()->get('tixi_app.routingmachine');
        $routeRepo = $this->getContainer()->get('route_repository');

        $routesToUpdate = $routeRepo->findRoutesOlderThenOneMonth();
        $toUpdate = count($routesToUpdate);

        $error = false;
        try {
            $routeMachine->fillRoutingInformationForMultipleRoutes($routesToUpdate);
            $em->flush();
        } catch (\Exception $e) {
            $error = true;
        }

        //Mail changes
        $mailService = $this->getContainer()->get('tixi_app.mailservice');
        $renderView = $this->getContainer()->get('templating');
        $translator = $this->getContainer()->get('translator');

        $subject = $translator->trans('routeupdate.subject');
        $title = $translator->trans('routeupdate.title');
        if ($error) {
            $body = $translator->trans('routeupdate.descr_error');
        } else {
            $body = $translator->trans('routeupdate.descr');
            $body = $toUpdate . ' ' . $body;
        }

        $body = $toUpdate . $body;

        $mailTo[] = $this->getContainer()->getParameter('tixi_parameter_admin_mail');
        $html = $renderView->render(
            'TixiAppBundle:mail:infoMail.html.twig',
            array(
                'mail_parameter_name' => $title,
                'mail_parameter_body' => $body,
            )
        );

        $mailService->mailToSeveralRecipients($mailTo, $subject, $html);
    }
}