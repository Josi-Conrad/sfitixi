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
            $limit = 1000;
        }

        $em = $this->getContainer()->get('entity_manager');
        $addressRep = $this->getContainer()->get('address_repository');
        $addressService = $this->getContainer()->get('tixi_app.addressmanagement');
        $routingMachine = $this->getContainer()->get('tixi_app.routingmachine');

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

                $cord = $routingMachine->getNearestPointsFromCoordinates($address->getLat(), $address->getLng());
                if ($cord !== null) {
                    $address->setNearestLat($cord->getLatitude());
                    $address->setNearestLng($cord->getLongitude());
                    $changed++;
                }
            } else {
                echo " no valid address found\n";
            }

            if ($count == $limit) {
                break;
            }
        }
        $em->flush();

        $addressNearest = $addressRep->findAddressesWithoutNearestCoordinates();
        $countNearest = 0;
        $changedNearest = 0;
        /**@var $address Address */
        foreach ($addressNearest as $address) {
            $countNearest++;
            echo $address->toString() . " nearest points: ";
            if ($address->gotCoordinates()) {
                /**@var $cord \Tixi\App\AppBundle\Routing\RoutingCoordinate */
                $cord = $routingMachine->getNearestPointsFromCoordinates($address->getLat(), $address->getLng());
                if ($cord !== null) {
                    echo $cord->getLatitude() . " " . $cord->getLongitude() . "\n";
                    $address->setNearestLat($cord->getLatitude());
                    $address->setNearestLng($cord->getLongitude());
                    $changedNearest++;
                }
            }
            if ($countNearest == $limit) {
                break;
            }
        }
        $em->flush();

        //Mail changes
        $mailService = $this->getContainer()->get('tixi_app.mailservice');

        $renderView = $this->getContainer()->get('templating');
        $translator = $this->getContainer()->get('translator');

        $subject = $translator->trans('addrquery.subject');
        $title = $translator->trans('addrquery.title');
        $body = $translator->trans('addrquery.descr1') . ' ' . $changedNearest . ' ' . $translator->trans('addrquery.descr2');

        $mailTo[] = $this->getContainer()->getParameter('tixi_parameter_admin_mail');
        $html = $renderView->render(
            'TixiAppBundle:mail:infoMail.html.twig',
            array(
                'mail_parameter_name' => $title,
                'mail_parameter_body' => $body,
            )
        );

        $mailService->mailToSeveralRecipients($mailTo, $subject, $html);

        $output->writeln("\n--------------------------------------------\n" .
            "Addresses without coordinates: " . $count . "\n" . "Addresses Updated: " . $changed . "\n" .
            "Addresses only without nearest coordinates: " . $countNearest . "\n" . "Addresses Updated: " . $changedNearest . "\n");
    }
}