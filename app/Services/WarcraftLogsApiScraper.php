<?php

namespace App\Services;

// TODO: Make this whole thing static?
class WarcraftLogsApiScraper
{
    private $data;

    // Zone ID's
    private $zoneIds = [ 'theEmeraldNightmare' => 10, 'theNighthold' => 11, 'trialOfValor' => 12 ];

    // Encounter ID's
    private $enEncounterIds = [1853, 1873,1876,1841,1854,1877,1864];
    private $tovEncounterIds = [1958,1962,2008];
    private $nhEncounterIds = [1849,1865,1867,1871,1862,1863,1842,1886,1872,1866];

    public function __construct()
    {
        $this->data = [];
    }

    public function scrape($rawData)
    {
        $this->getZoneAverageRanks($rawData);

        return $this->data;
    }

    /**
     * Extracts Nighthold encounter rankings.
     *
     * @param $rawData
     */
    public function getEncounterRankings($rawData) {
        foreach ($rawData as $encounter) {
            $this->data[$encounter['encounter']] = [
                $encounter['rank'],
                $encounter['outOf']
            ];
        }
    }

    /**
     *
     * @param $rawData
     */
    public function getZoneAverageRanks($rawData) {
        $this->data = [
            'average_rank_en_normal' => 0,
            'average_rank_en_heroic' => 0,
            'average_rank_en_mythic' => 0,
            'average_rank_tov_normal' => 0,
            'average_rank_tov_heroic' => 0,
            'average_rank_tov_mythic' => 0,
            'average_rank_nh_normal' => 0,
            'average_rank_nh_heroic' => 0,
            'average_rank_nh_mythic' => 0,
        ];

        $temp = [];

        foreach ($this->data as $key => $value) {
            $temp[$key]['ratio'] = 0;
            $temp[$key]['count'] = 0;
        }

        foreach ($rawData as $zones) {
            foreach ($zones as $encounter) {
                if(in_array($encounter['encounter'],$this->enEncounterIds)) {
                    if($encounter['difficulty'] == 3) {
                        $temp['average_rank_en_normal']['ratio'] += (1 - ($encounter['rank'] / $encounter['outOf']));
                        $temp['average_rank_en_normal']['count']++;
                    } else if($encounter['difficulty'] == 4) {
                        $temp['average_rank_en_heroic']['ratio'] += (1 - ($encounter['rank'] / $encounter['outOf']));
                        $temp['average_rank_en_heroic']['count']++;
                    } else if($encounter['difficulty'] == 5) {
                        $temp['average_rank_en_mythic']['ratio'] += (1 - ($encounter['rank'] / $encounter['outOf']));
                        $temp['average_rank_en_mythic']['count']++;
                    }
                }
                if(in_array($encounter['encounter'],$this->tovEncounterIds)) {
                    if($encounter['difficulty'] == 3) {
                        $temp['average_rank_tov_normal']['ratio'] += (1 - ($encounter['rank'] / $encounter['outOf']));
                        $temp['average_rank_tov_normal']['count']++;
                    } else if ($encounter['difficulty'] == 4) {
                        $temp['average_rank_tov_heroic']['ratio'] += (1 - ($encounter['rank'] / $encounter['outOf']));
                        $temp['average_rank_tov_heroic']['count']++;
                    } else if ($encounter['difficulty'] == 5) {
                        $temp['average_rank_tov_mythic']['ratio'] += (1 - ($encounter['rank'] / $encounter['outOf']));
                        $temp['average_rank_tov_mythic']['count']++;
                    }
                }
                if(in_array($encounter['encounter'],$this->nhEncounterIds)) {
                    if($encounter['difficulty'] == 3) {
                        $temp['average_rank_nh_normal']['ratio'] += (1 - ($encounter['rank'] / $encounter['outOf']));
                        $temp['average_rank_nh_normal']['count']++;
                    } else if ($encounter['difficulty'] == 4) {
                        $temp['average_rank_nh_heroic']['ratio'] += (1 - ($encounter['rank'] / $encounter['outOf']));
                        $temp['average_rank_nh_heroic']['count']++;
                    } else if ($encounter['difficulty'] == 5) {
                        $temp['average_rank_nh_mythic']['ratio'] += (1 - ($encounter['rank'] / $encounter['outOf']));
                        $temp['average_rank_nh_mythic']['count']++;
                    }
                }
            }
        }

        $this->data['average_rank_en_normal'] = ($temp['average_rank_en_normal']['ratio'] / (($temp['average_rank_en_normal']['count'] == 0) ? 1 : $temp['average_rank_en_normal']['count'])) * 100;
        $this->data['average_rank_en_heroic'] = ($temp['average_rank_en_heroic']['ratio'] / (($temp['average_rank_en_heroic']['count'] == 0) ? 1 : $temp['average_rank_en_heroic']['count'])) * 100;
        $this->data['average_rank_en_mythic'] = ($temp['average_rank_en_mythic']['ratio'] / (($temp['average_rank_en_mythic']['count'] == 0) ? 1 : $temp['average_rank_en_mythic']['count'])) * 100;

        $this->data['average_rank_tov_normal'] = ($temp['average_rank_tov_normal']['ratio'] / (($temp['average_rank_tov_normal']['count'] == 0) ? 1 : $temp['average_rank_tov_normal']['count'])) * 100;
        $this->data['average_rank_tov_heroic'] = ($temp['average_rank_tov_heroic']['ratio'] / (($temp['average_rank_tov_heroic']['count'] == 0) ? 1 : $temp['average_rank_tov_heroic']['count'])) * 100;
        $this->data['average_rank_tov_mythic'] = ($temp['average_rank_tov_mythic']['ratio'] / (($temp['average_rank_tov_mythic']['count'] == 0) ? 1 : $temp['average_rank_tov_mythic']['count'])) * 100;

        $this->data['average_rank_nh_normal'] = ($temp['average_rank_nh_normal']['ratio'] / (($temp['average_rank_nh_normal']['count'] == 0) ? 1 : $temp['average_rank_nh_normal']['count'])) * 100;
        $this->data['average_rank_nh_heroic'] = ($temp['average_rank_nh_heroic']['ratio'] / (($temp['average_rank_nh_heroic']['count'] == 0) ? 1 : $temp['average_rank_nh_heroic']['count'])) * 100;
        $this->data['average_rank_nh_mythic'] = ($temp['average_rank_nh_mythic']['ratio'] / (($temp['average_rank_nh_mythic']['count'] == 0) ? 1 : $temp['average_rank_nh_mythic']['count'])) * 100;

        foreach ($this->data as $key => $value) {
            $this->data[$key] = intval(round($value));
        }
    }
}