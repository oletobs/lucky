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

        foreach ($rawData as $encounter) {
            if(in_array($encounter['encounter'],$this->enEncounterIds)) {
                $ratio = 0;
                $i = 0;
                if($encounter['difficulty'] == 5) {
                    $ratio += ($encounter['outOf']-$encounter['rank'] / $encounter['outOf']);
                    $i++;
                }
                $this->data['average_rank_en_mythic'] = ($ratio / $i) * 100;
            }
        }
    }
}