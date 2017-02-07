<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

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

    public function setClient($client) {
        $this->client = $client;
    }

    public function get($url, array $queries)
    {
        $queries['api_key'] = $this->apiKey;

        return $this->client->get($url, ['query' => $queries]);
    }

    public function getAsync($url, array $queries)
    {
        $queries['api_key'] = $this->apiKey;

        return $this->client->getAsync($url, ['query' => $queries]);
    }


    public function getRankingsAsync($region, $server, $name, array $zones = [10,11,12], $encounter = null, $metric = 'dps')
    {
        $url = sprintf(self::BASE_URL, 'rankings', 'character',$name , $server, $region);
        $data = [];

        if(isset($encounter)) {
            $queries['encounter'] = $encounter;
        }

        $queries['metric'] = $metric;

        $promises = [];
        foreach ($zones as $zone) {
            $queries['zone'] = $zone;
            $promises[] = $this->getAsync($url, $queries);
        }

        $results = Promise\unwrap($promises);
        for($i = 0; $i < count($zones); $i++) {
            $body = json_decode($results[$i]->getBody(),true);
            $data[] = $body;
        }

        return $data;
    }

    public function getRankingsPromises($region, $server, $name, array $zones = [10,11,12], $encounter = null, $metric = 'dps')
    {
        $url = sprintf(self::BASE_URL, 'rankings', 'character',$name , $server, $region);

        if(isset($encounter)) {
            $queries['encounter'] = $encounter;
        }

        $queries['metric'] = $metric;

        $promises = [];
        foreach ($zones as $zone) {
            $queries['zone'] = $zone;
            $promises[$zone] = $this->getAsync($url, $queries);
        }

        return $promises;
    }
}