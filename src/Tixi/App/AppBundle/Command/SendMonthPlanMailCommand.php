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
 * Class SendMonthPlanMailCommand
 * @package Tixi\App\AppBundle\Command
 */
class SendMonthPlanMailCommand extends ContainerAwareCommand {
    public function configure() {
        $this->setName('project:send-monthplan')
            ->setDescription('Send Monthplan to drivers');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output) {

        $date = \DateTime::createFromFormat('d.m.Y', '01.07.2024');

        //Mail changes
        $documentManager = $this->getContainer()->get('tixi_app.documentmanagement');
        $documentManager->sendMonthPlanToAllDrivers($date);

    }
}