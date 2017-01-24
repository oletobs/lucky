<?php

namespace App\Services;

// TODO: Make this whole thing static?
class BlizzardApiScraper
{
    private $data;

    // Encounter ID's
    private $mythicDungeonIds = [10880,10883,10886,10889,10898,10901,10904,10907,10910,11406];
    private $lfrIds           = [10911,10920,10924,10915,10928,10932,10936,11407,11411,11415,10940,10944,10948,10952,10956,10961,10965,10969,10973,10977];
    private $normalRaidIds    = [10912,10921,10925,10916,10929,10933,10937,11408,11412,11416,10941,10945,10949,10953,10957,10962,10966,10970,10974,10978];
    private $heroicRaidIds    = [10913,10922,10926,10917,10930,10934,10938,11409,11413,11417,10942,10946,10950,10954,10959,10963,10967,10971,10975,10979];
    private $mythicRaidIds    = [10914,10923,10927,10919,10931,10935,10939,11410,11414,11418,10943,10947,10951,10955,10960,10964,10968,10972,10976,10980];

    // Achievement Criteria ID's
    private $totalArtifactPowerId = '30103';
    private $maxArtifactLevelId   = '29395';
    private $totalWorldQuestsId   = '33094';
    private $mythicLevelTwo       = '33096';
    private $mythicLevelFive      = '33097';
    private $mythicLevelTen       = '33098';
    private $mythicLevelFifteen   = '32028';

    public function __construct()
    {
        $this->data = [];
    }

    public function scrape($rawData)
    {
        $this->getBossKills($rawData);
        $this->sumKillStats();
        $this->getItemLevels($rawData);
        $this->getAchievementData($rawData);

        return $this->data;
    }

    /**
     * Extracts PVE encounter kill data from default json response.
     *
     * Result formated as
     * "kills": [ encounter_id => number_of_kills ]
     *
     * @param $rawData
     */
    public function getBossKills($rawData) {
        foreach ($rawData['statistics']['subCategories'][5]['subCategories'][6]['statistics'] as $encounter) {
            $this->data['kills'][$encounter['id']] = $encounter['quantity'];
        }
    }

    /**
     * Sums the combined kills of PVE encounter under different categories.
     */
    public function sumKillStats() {
        $names = [
            'total_mythic_dungeons',
            'total_mythic_raid_bosses',
            'total_heroic_raid_bosses',
            'total_normal_raid_bosses',
            'total_lfr_raid_bosses',
            'total_raid_bosses',
        ];

        foreach ($names as $name) {
            $this->data[$name] = 0;
        }

        $this->sumEncounter('total_mythic_dungeons',$this->mythicDungeonIds);
        $this->sumEncounter('total_mythic_raid_bosses',$this->mythicRaidIds);
        $this->sumEncounter('total_heroic_raid_bosses',$this->heroicRaidIds);
        $this->sumEncounter('total_normal_raid_bosses',$this->normalRaidIds);
        $this->sumEncounter('total_lfr_raid_bosses',$this->lfrIds);
        $this->data['total_raid_bosses'] = $this->data['total_lfr_raid_bosses']
            +$this->data['total_normal_raid_bosses']
            +$this->data['total_heroic_raid_bosses']
            +$this->data['total_mythic_raid_bosses'];
    }

    /**
     * Sums a single encounter category, based on a list of encounter ids and a name to store the result under.
     * The encounters must be stored under kills, as by getBossKills().
     *
     * @param $name
     * @param $encounterIds
     */
    private function sumEncounter($name,$encounterIds) {
        foreach ($encounterIds as $encounterId) {
            $this->data[$name] += $this->data['kills'][$encounterId];
        }
    }

    /**
     * Extracts average, equipped average and equipped artifact item level.
     *
     * @param $rawData
     */
    public function getItemLevels($rawData) {
        $this->data['equipped_ilvl'] = $rawData['items']['averageItemLevelEquipped'];
        $this->data['average_ilvl'] = $rawData['items']['averageItemLevel'];

        if(isset($rawData['items']['mainHand'])) {
            $this->data['artifact_ilvl'] = $rawData['items']['mainHand']['itemLevel'];
        } elseif(isset($body['items']['offHand'])) {
            $this->data['artifact_ilvl'] = $rawData['items']['offHand']['itemLevel'];
        }
    }

    /**
     * Extracts useful information hidden as criteria for different achievements.
     *
     * @param $rawData
     */
    public function getAchievementData($rawData) {
        $keyALvl = array_search($this->maxArtifactLevelId, $rawData['achievements']['criteria']);
        $keyAP = array_search($this->totalArtifactPowerId, $rawData['achievements']['criteria']);
        $keyWQ = array_search($this->totalWorldQuestsId, $rawData['achievements']['criteria']);
        $keyMFifteen = array_search($this->mythicLevelFifteen, $rawData['achievements']['criteria']);
        $keyMFive = array_search($this->mythicLevelFive, $rawData['achievements']['criteria']);
        $keyMTwo = array_search($this->mythicLevelTwo, $rawData['achievements']['criteria']);
        $keyMTen = array_search($this->mythicLevelTen, $rawData['achievements']['criteria']);

        $this->data['highest_artifact_ilvl'] = $rawData['achievements']['criteriaQuantity'][$keyALvl];
        $this->data['total_ap'] = $rawData['achievements']['criteriaQuantity'][$keyAP];
        $this->data['total_wq'] = $rawData['achievements']['criteriaQuantity'][$keyWQ];
        $this->data['total_mythic_2'] = $rawData['achievements']['criteriaQuantity'][$keyMTwo];
        $this->data['total_mythic_5'] = $rawData['achievements']['criteriaQuantity'][$keyMFive];
        $this->data['total_mythic_10'] = $rawData['achievements']['criteriaQuantity'][$keyMTen];
        $this->data['total_mythic_15'] = $rawData['achievements']['criteriaQuantity'][$keyMFifteen];

        // Removing inconsistent stats
        if($this->data['total_mythic_2'] > $this->data['total_mythic_dungeons']) {
            $this->data['total_mythic_2'] = 0;
        }

        if($this->data['total_mythic_5'] > $this->data['total_mythic_2']) {
            $this->data['total_mythic_5'] = 0;
        }

        if($this->data['total_mythic_10'] > $this->data['total_mythic_5']) {
            $this->data['total_mythic_10'] = 0;
        }
    }
}