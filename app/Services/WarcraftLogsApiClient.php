<?php

namespace App\Services;

use GuzzleHttp\Client;

class WarcraftLogsApiClient
{
    //
    const BASE_URL = 'https://www.warcraftlogs.com:443/v1/%s/%s/%s/%s/%s';

    private $apiKey;

    private $client;

    public function __construct($apiKey, Client $client)
    {
        $this->apiKey = $apiKey;
        $this->client = $client;
    }

    public function get($url, array $queries)
    {
        $queries['api_key'] = $this->apiKey;

        return $this->client->get($url, ['query' => $queries]);
    }

    public function getRankings($region, $server, $name, $zone = null, $encounter = null, $metric = 'dps')
    {
        $url = sprintf(self::BASE_URL, 'rankings', 'character',$name , $server, $region);

        if(isset($zone)) {
            $queries['zone'] = $zone;
        }

        if(isset($encounter)) {
            $queries['encounter'] = $encounter;
        }

        $queries['metric'] = $metric;

        return $this->get($url, $queries);
    }
}