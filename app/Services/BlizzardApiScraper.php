<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

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

    // Mythic+ bonus ids
    private $mythicDungeonBonusIds = [3410,3411,3412,3413,3414,3415,3416,3417,3418,3509,3510,3534,3535,3536];

    // Item quality
    private $itemQuality = [ 1 => 'common', 2 => 'uncommon', 3 => 'rare', 4 => 'epic', 5 => 'legendary', 6 => 'artifact', 7 => 'heirloom' ];

    public function __construct()
    {
        $this->data = [];
    }

    public function scrape($rawData)
    {
        $this->getBossKills($rawData['statistics']);
        $this->sumKillStats();
        $this->getItemLevels($rawData['items']);
        $this->getAchievementData($rawData['achievements']);
        $this->getItems($rawData['items']);

        return $this->data;
    }

    /**
     * Extract item data from default json response.
     *
     * @param $rawData
     */
    public function getItems($rawData) {
        $this->data['equipped_dungeon_mythic_plus'] = 0;
        $this->data['equipped_raid_normal'] = 0;
        $this->data['equipped_raid_heroic'] = 0;
        $this->data['equipped_raid_mythic'] = 0;
        $this->data['equipped_dungeon_mythic'] = 0;
        $this->data['equipped_weekly_chest'] = 0;

        foreach ($rawData['items'] as $key => $item) {
            if(is_array($item)) {
                if($key !== 'tabard' && $key !== 'shirt') {
                    if($key == 'mainHand' || $key == 'offHand') {
                        $this->data['artifact'][$key] = [
                            'id' => $item['id'],
                            'icon' => $item['icon'],
                            'bonus' => $item['bonusLists'],
                            'quality' => $this->itemQuality[$item['quality']],
                            'item_level' => $item['itemLevel']
                        ];

                        // TODO: Make wowhead stuff into a separate component
                        if(array_key_exists('relics',$item)) {
                            $i = 0;
                            $client = new Client();
                            $promises = [];
                            \Debugbar::startMeasure('wowhead','Querying wowhead');
                            foreach ($item['relics'] as $relic) {
                                $this->data['relics'][] = [
                                    'id' => $relic['itemId'],
                                    'bonus' => $relic['bonusLists'],
                                ];
                                $promises[$i++] = $client->getAsync('http://www.wowhead.com/item='.$relic['itemId'].'&bonus='.implode(":", $relic['bonusLists']).'&xml');
                            }

                            try {
                                $results = Promise\unwrap($promises);
                                for($j = 0; $j < $i; $j++) {
                                    $xml = simplexml_load_string($results[$j]->getBody());
                                    $json = json_encode($xml);
                                    $wowhead = json_decode($json,true);

                                    $this->data['relics'][$j]['icon'] = $wowhead['item']['icon'];
                                    $this->data['relics'][$j]['item_level'] = intval($wowhead['item']['level']);
                                    $this->data['relics'][$j]['quality'] = strtolower($wowhead['item']['quality']);
                                }
                            } catch (\Exception $e) {
                                echo $e->getMessage();
                            }
                            \Debugbar::stopMeasure('wowhead');
                        }
                    } else {

                        $this->data['items'][$key] =  [
                            'id' => $item['id'],
                            'bonus' => $item['bonusLists'],
                            'icon' => array_key_exists('icon',$item) ? $item['icon'] : 'inv_misc_questionmark',
                            'quality' => $this->itemQuality[$item['quality']],
                            'item_level' => $item['itemLevel']
                        ];

                        if(strpos($item['context'],'raid-heroic') !== false) {
                            $this->data['equipped_raid_heroic']++;
                        } elseif(strpos($item['context'],'raid-mythic') !== false) {
                            $this->data['equipped_raid_mythic']++;
                        } elseif(strpos($item['context'],'dungeon-mythic') !== false) {
                            $this->data['equipped_dungeon_mythic_plus']++;
                            //$this->data['equipped_dungeon_mythic']++;
                        } elseif(strpos($item['context'],'challenge-mode-jackpot') !== false) {
                            $this->data['equipped_weekly_chest']++;
                        } elseif(strpos($item['context'],'challenge-mode') !== false) {
                            $this->data['equipped_dungeon_mythic_plus']++;
                        } elseif(strpos($item['context'],'raid-normal') !== false) {
                            $this->data['equipped_raid_normal']++;
                        }


                    }
                }
            }
        }

        $this->data['equipped_raid_total'] = $this->data['equipped_raid_normal']+$this->data['equipped_raid_heroic']+$this->data['equipped_raid_mythic'];
        $this->data['equipped_dungeon_total'] =  $this->data['equipped_weekly_chest']+$this->data['equipped_dungeon_mythic_plus'];
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
        $this->data['equipped_average_ilvl_diff'] = $rawData['items']['averageItemLevel'] - $rawData['items']['averageItemLevelEquipped'];

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