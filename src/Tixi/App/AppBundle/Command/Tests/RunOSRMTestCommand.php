<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 29.03.14
 * Time: 17:53
 */

namespace Tixi\App\AppBundle\Command\Tests;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tixi\CoreDomain\Address;
use Tixi\CoreDomain\Dispo\Route;

/**
 * Runs multiple OSRM Route Queries for testing purpose
 * Class RunOSRMTestCommand
 * @package Tixi\App\AppBundle\Command\Tests
 */
class RunOSRMTestCommand extends ContainerAwareCommand {
    public function configure() {
        $this->setName('project:osrm-test')
            ->setDescription('Runs routing queries on OSRM Server')
            ->addArgument('n', InputArgument::REQUIRED, 'How many routes?');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $n = $input->getArgument('n');

        $routingMachine = $this->getContainer()->get('tixi_app.routingmachine');

        $address1 = Address::registerAddress('Rathausstrasse 1', '6340',
            'Baar', 'Schweiz', 'Ganztagesschule mit Montessoriprofil', 47.194715, 8.526096);
        $address2 = Address::registerAddress('Bahnhofstrasse 9', '6410',
            'Arth', 'Schweiz', 'CSS', 47.049536, 8.547931);

        $routes = array();

        for ($i = 0; $i < $n; $i++) {
            array_push($routes, Route::registerRoute($address1, $address2));
        }
        $startTime = microtime(true);
        $routingMachine->fillRoutingInformationForMultipleRoutes($routes);
        $endTime = microtime(true);
        $output->writeln('Exectued ' . $n . ' OSRM requests in: ' . ($endTime - $startTime) . "s\n");
    }
}