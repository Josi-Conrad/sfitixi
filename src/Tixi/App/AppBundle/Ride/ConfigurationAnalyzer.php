<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 28.05.14
 * Time: 13:15
 */

namespace Tixi\App\AppBundle\Ride;

/**
 * Class ConfigurationAnalyzer
 * @package Tixi\App\AppBundle\Ride
 */
class ConfigurationAnalyzer {
    /**
     * @param RideNode $rideNode
     * @return bool
     */
    public function checkIfNodeIsFeasibleInConfiguration(RideNode $rideNode) {
        $feasible = false;
        foreach ($this->rideConfiguration->getRideNodeLists() as $rideNodeList) {
            /**@var RideNode $lastNode */
            $lastNode = null;
            foreach ($rideNodeList->getRideNodes() as $node) {
                if ($lastNode === null) {
                    $lastNode = $node;
                    if ($rideNode->endMinute < $lastNode->startMinute) {
                        $feasible = true;
                    }
                    continue;
                }

                if ($rideNode->startMinute < $lastNode->endMinute && $rideNode->endMinute < $node->startMinute
                    || $rideNode->endMinute < $lastNode->startMinute
                ) {
                    $feasible = true;
                }
                $lastNode = $node;
            }

            if ($rideNode->startMinute > $lastNode->endMinute) {
                $feasible = true;
            }
        }
        return $feasible;
    }

} 