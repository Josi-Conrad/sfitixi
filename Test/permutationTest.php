<?php
/**
 * Created by PhpStorm.
 * User: faustos
 * Date: 22.05.14
 * Time: 15:50
 */
$testArray = array();
for($i=0;$i<5;$i++) {
    $testArray[] = $i;
}
$configurationsArray = array();

//print_r(permutation($testArray, count($testArray), new Configuration(), $configurationsArray, true));
$configurations = createConfigurations($testArray);
//$test = 'a';
print_r($configurations);



function createConfigurations($array) {
    $configurations = array();
    for($i=0;$i<count($array);$i++) {
        $newConfigurations = array();
        if(count($configurations)===0) {
            $configurations[] = new Configuration();
        }
        /** @var Configuration $configuration */
        foreach($configurations as $configuration) {
            /** @var MissionList $list */
            $oldMissionList = $configuration->getMissionLists();
            for($k=0;$k<count($oldMissionList);$k++) {
                $newMissionList = $configuration->getClonedMissionLists();
                if($newMissionList[$k]->addMission($array[$i])) {
                    $newConfigurations[] = new Configuration($newMissionList);
                }
            }
            if(count($configuration->getMissionLists())<18) {
                $newConfigurations[] = $configuration;
                $configuration->addMissionList(new MissionList($array[$i]));
            }
        }
        $configurations = $newConfigurations;
        print(count($configurations)."\xA");
    }
    return $configurations;
}

function permutation($array, $n, $configuration, &$configurationsArray, $count) {
    $n--;
    if($n<0) {
        return $configurationsArray;
    }
    $configuration->addMissionList(new MissionList($array[$n]));
    $missionLists = $configuration->getMissionLists();
    for($i=0;$i<count($missionLists)-1;$i++) {

    }

//    for($i=0;$i<count($configuration->getMissionLists());$i++) {
//
//    }

    foreach($configuration->getMissionLists() as $list) {
        $list->addMission($array[$n]);

    }




    return permutation($array, $n, $configuration, $configurationsArray, true);
}



class MissionList {

    protected $missions;
    protected $totalDistance;

    public static $count=0;

    public function __construct($mission=null) {
        $this->missions = array();
        if(null !== $mission) {
            $this->missions[] = $mission;
        }
    }



    public function addMission($mission) {
        $this->missions[] = $mission;
        return (count($this->missions)<6 || ((self::$count++)%2));
    }

    /**
     * @return mixed
     */
    public function getMissions()
    {
        return $this->missions;
    }

    /**
     * @return mixed
     */
    public function getTotalDistance()
    {
        return $this->totalDistance;
    }



}

class Configuration {
    protected $missionLists;


    public function __construct($missionList=null) {
        if(null !== $missionList) {
            $this->missionLists = $missionList;
        }else {
            $this->missionLists = array();
        }
    }

    public function addMissionList(MissionList $missionList) {
        $this->missionLists[] = $missionList;
    }

    /**
     * @return mixed
     */
    public function getMissionLists()
    {
        return $this->missionLists;
    }

    public function getClonedMissionLists() {
        $clonedLists = array();
        foreach($this->missionLists as $list) {
            $missionList = new MissionList();
            foreach($list->getMissions() as $mission) {
                $missionList->addMission($mission);
            }
            $clonedLists[] = $missionList;
        }
        return $clonedLists;
    }

}



