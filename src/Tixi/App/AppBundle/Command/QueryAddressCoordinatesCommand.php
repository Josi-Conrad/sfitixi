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
use Tixi\CoreDomain\Dispo\Route;

/**
 * Class QueryAddressCoordinatesCommand
 * @package Tixi\App\AppBundle\Command
 */
class QueryAddressCoordinatesCommand extends ContainerAwareCommand {
    public function configure() {
        $this->setName('project:query-address-coordinates')
            ->setDescription('Queries addresses in DB. If no address coordinates available, set them by lookup with an address service')
            ->addArgument('limit', InputArgument::OPTIONAL, 'Limit the amount of queries');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $limit = $input->getArgument('limit');
        if (!$limit) {
            $limit = 100;
        }

        $em = $this->getContainer()->get('entity_manager');
        $addressRep = $this->getContainer()->get('address_repository');
        $addressService = $this->getContainer()->get('tixi_app.addressmanagement');

        $addresses = $addressRep->findAddressesWithoutCoordinates();
        $count = 0;
        $changed = 0;
        /**@var $address Address */
        foreach ($addresses as $address) {
            $count++;
            echo $address->toString() . ": ";

            $addressDTO = $addressService->getAddressInformationByString($address->toString());
            if ($addressDTO !== null) {
                echo $addressDTO->lat . " " . $addressDTO->lng . "\n";
                $address->setLat($addressDTO->lat);
                $address->setLng($addressDTO->lng);
                $changed++;
            }

            if ($count == $limit) {
                break;
            }
        }

        $em->flush();

        $output->writeln("\n--------------------------------------------\n" .
            "Addresses without coordinates: " . $count . "\n" . "Addresses Updated: " . $changed . "\n");
    }
}